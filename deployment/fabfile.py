import datetime
import os, glob
import os.path
import sys

from fabric.api import local, run, sudo, env, settings
from fabric.colors import green, red, white, _wrap_with
from fabric.context_managers import cd, lcd
from fabric.operations import put, prompt
from fabric.contrib.files import upload_template

from fabconfig import *


# Utils

def _get_commit_id():
    "Returns the commit ID for the branch about to be deployed"
    return local('git rev-parse HEAD', capture=True)[:20]

def getUser():
    env.user = prompt('Username for remote host? [default is current user]')
    os.environ['TANGENT_USER'] = env.user

    if not env.user:
        env.user = os.environ['USER']

# Main Task

def deploy():
    "Builds Grabagame"

    #Make sure we're in the top dir
    check_deployment_dir()

    # Look for user to use
    getUser()

    # Ensure we deploy the latest code
    update_codebase()

    # Create Build
    commit_id = _get_commit_id()
    print(green("Building from revision %s" % commit_id))

    archive_file = '/tmp/build-%s.tar.gz' % str(commit_id)
    prepare_build(archive_file, env.branch)

    # Upload and unpack
    upload(archive_file)
    unpack(archive_file)

    make_cache_and_log_dirs()
    set_cache_and_log_permissions()

    # Symlinks to production release
    if env.Environment == 'live':
        set_production_symlinks()

    installVendors()
    updateVendors()
    installAssets()
    
    rename_robots()

    clear_caches()
    set_cache_and_log_permissions()

    # Change Permissions
    if env.Environment == 'live':
        apply_production_permissions()
    else:
        apply_sftp_friendly_permissions()

# Tasks
def check_deployment_dir():
    "Checks the deployment dir for a file to make sure we're in the top directory"

    print green('Checking to make sure deploying from top level dir...')
    if os.path.exists('deployment/allowDeploy'):
        print green('OK, must be in top level dir')
    else:
        print red('Not in top level dir (or allowDeploy does not exist in deployment dir)')
        sys.exit()


def update_codebase():
    "Updates the codebase from the Git repo"

    print green('Updating codebase from remote "%s", branch "%s"' % (env.repo, env.branch))
    local('git checkout %s' % (env.branch))

    if env.Environment != 'live':   
        print green('Pushing any local changes to remote "%s", branch "%s"' % (env.repo, env.branch))
        local('git push %s %s' % (env.repo, env.branch))
        local('git pull %s %s' % (env.repo, env.branch))


def prepare_build(archive_file, reference='master'):
    "Creates a gzipped tarball with the code to be deployed"

    local('git archive %s | gzip > %s ' % (reference, archive_file))


def upload(local_path, remote_path=None):
    "Uploads a file"

    if not remote_path:
        remote_path = local_path
    print(green("Uploading %s to %s" % (local_path, remote_path)))
    put(local_path, remote_path)
    
    local('rm -f %s' % local_path)

def make_dir(directory):
    print(green("Making directory %s" % directory))
    sudo('mkdir %s' % directory)

def unpack(archive_path, temp_folder = '/tmp/build_temp'):
    "Unpacks the tarball into the correct place"

    print(green("Creating build folder"))

    # Create temp folder
    run('if [ -d "%s" ]; then rm -rf "%s"; fi' % (temp_folder,temp_folder))
    run('mkdir -p %s' % temp_folder)

    with cd('%s' % temp_folder):
        run('tar xzf %s' % archive_path)

        # Create new build folder
        sudo('if [ -d "%(BuildRoot)s" ]; then rm -rf "%(BuildRoot)s"; fi' % env)
        sudo('mkdir -p %s' % env.BuildRoot)

    # Move src to build
    sudo('mv %s/src/* %s' % (temp_folder, env.BuildRoot))

    # Create Application Configuration File
    create_parameters_ini()
    rename_htaccess()

    # Deleted Temporal Files and Directories
    run('rm -rf %s' % temp_folder)
    run('rm -f %s' % archive_path)


def create_remote_folder(folder_path):
    "Creates a remote folder"

    sudo('if [ -d "%s" ]; then rm -rf "%s"; fi' % (folder_path,folder_path))
    sudo('mkdir -p %s' % folder_path)


def create_parameters_ini(temp_folder = '/tmp/build_temp'):
    "Creates parameters.ini"

    if env.Environment == 'live':
        with cd('%s' % temp_folder):
            upload_template('src/app/config/parameters.ini.live', 'parameters.ini', context=env)
    else:
        with cd('%s' % temp_folder):
            upload_template('src/app/config/parameters.ini.dist', 'parameters.ini', context=env)

    with cd(env.BuildRoot):
        sudo('rm -f parameters.ini')
        sudo('rm -f parameters.tpl')
        sudo('mv %s/parameters.ini %s/app/config' % (temp_folder,env.BuildRoot))

    with cd('%s' % temp_folder):
        run('rm -f %s/parameters.ini' % temp_folder)

def rename_htaccess():
    "Renames htaccess files"

    if env.Environment == 'live':
        run('mv %(BuildRoot)s/web/.htaccess.live %(BuildRoot)s/web/.htaccess' % env)
        run('rm %(BuildRoot)s/web/.htaccess.dist' % env)
    else:
        run('mv %(BuildRoot)s/web/.htaccess.dist %(BuildRoot)s/web/.htaccess' % env)
        run('rm %(BuildRoot)s/web/.htaccess.live' % env)

def set_production_symlinks():
    "Create production symbolic links"

    sudo('if [ -h %(AppRoot)s/builds/live/latest ]; then unlink %(AppRoot)s/builds/live/latest; fi' % env)
    sudo('ln -s %(AppRoot)s/builds/live/%(tag)s %(AppRoot)s/builds/live/latest' % env )


def apply_sftp_friendly_permissions():
    "Apply friendly permissions"

    sudo('chmod -R 777 %(LogDir)s' % env)
    sudo('if [ -d "%(CacheDir)s" ]; then chmod -R 777 %(CacheDir)s; else mkdir %(CacheDir)s; chmod -R 777 %(CacheDir)s; fi' % env)
    sudo('chown -R root.www-data %(BuildRoot)s' % env)


def apply_production_permissions():
    "Apply production permissions"

    sudo('chmod -R ug+rw %(BuildRoot)s/app/logs' % env)
    sudo('chown -R www-data:www-data %(BuildRoot)s' % env)

def installVendors():
    "Install vendors"
    sudo('php %(BuildRoot)s/bin/vendors install' % env)

def updateVendors():
    "Update vendors"
    sudo('php %(BuildRoot)s/bin/vendors update' % env)

def installAssets():
    "Install assets to web"
    sudo('php %(BuildRoot)s/app/console assets:install %(BuildRoot)s/web' % env)

def clear_caches():
    "Clear caches"
    sudo('php %(BuildRoot)s/app/console cache:clear --env=dev --no-debug' % env)
    sudo('php %(BuildRoot)s/app/console cache:clear --env=prod --no-debug' % env)

def rename_robots():
    "Renaming robots.txt file"
    sudo('mv %s/app/config/robots.txt.%s %s/web/robots.txt' % (env.BuildRoot, env.Environment, env.BuildRoot))

def make_cache_and_log_dirs():
    sudo('if [ ! -d "%(CacheDir)s" ]; then mkdir %(CacheDir)s; fi' % env)
    sudo('if [ ! -d "%(LogDir)s" ]; then mkdir %(LogDir)s; fi' % env)
    sudo('ln -sv /var/log/grabagame-%(Environment)s %(BuildRoot)s/app/logs' % env)
    sudo('ln -sv /var/cache/grabagame-%(Environment)s %(BuildRoot)s/app/cache' % env)

def set_cache_and_log_permissions():
    sudo('if [ -d "%(CacheDir)s" ]; then chmod -R 777 %(CacheDir)s; fi' % env)
    sudo('if [ -d "%(LogDir)s" ]; then chmod -R 777 %(LogDir)s; fi' % env)
