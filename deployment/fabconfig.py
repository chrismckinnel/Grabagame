from fabric.api import env
from fabric.operations import prompt

# Environments

def dev():
    "Development Settings"

    env.hosts = ['69.55.55.190']
    env.Environment = 'dev'

    "Git Repository Settings"
    env.branch = 'develop'
    env.repo = 'git@github.com:chrismckinnel/Grabagame.git'

    "Application Paths"
    env.AppRoot = '/var/www/grabagame'

    "Build Settings"
    env.BuildName = 'dev'
    env.CacheDir = '/var/cache/grabagame-*'
    env.LogDir = '/var/log/grabagame'
    env.BuildRoot = '%(AppRoot)s/builds/dev' % env 

    "Domain Settings"
    env.BaseUrl = 'http://dev.grabagame.co.nz/'

    "Database settings"
    env.DatabaseName = 'Grabagame_dev'
    env.DatabaseUser = 'Grabagame'
    env.DatabasePassword = 'ooge5viej3roo8shoo3voXeoquudaesh'

    "Other settings"
    env.Secret = 'Yeux3woo2aihi5ohrohtijup7fieCh7g'

def test():
    "Test Settings"

    env.hosts = ['69.55.55.190']
    env.Environment = 'test'

    "Git Repository Settings"
    env.tag = prompt('Which release version do you want to deploy? [e.g: 1.1]')
    env.branch = 'release/%s' % env.tag
    env.repo = 'git@github.com:chrismckinnel/Grabagame.git'

    "Application Paths"
    env.AppRoot = '/var/www/grabagame'

    "Build Settings"
    env.BuildName = 'test'
    env.BuildRoot = '%(AppRoot)s/builds/test' % env 
    env.CacheDir = '/var/cache/grabagame'
    env.LogDir = '/var/log/grabagame'

    "Domain Settings"
    env.BaseUrl = 'http://test.grabagame.co.nz/'

    "Database settings"
    env.DatabaseName = 'Grabagame_test'
    env.DatabaseUser = 'Grabagame'
    env.DatabasePassword = 'ooge5viej3roo8shoo3voXeoquudaesh'

    "Other settings"
    env.Secret = 'Yeux3woo2aihi5ohrohtijup7fieCh7g'

def live():
    "Live Settings"

    env.hosts = ['69.55.55.190']
    env.Environment = 'live'

    "Git Repository Settings"
    env.tag = prompt('Which release version do you want to deploy? [e.g: v1.1]')
    env.branch = '%s' % env.tag
    env.repo = 'git@github.com:chrismckinnel/Grabagame.git'

    "Application Paths"
    env.AppRoot = '/var/www/grabagame'

    "Build Settings"
    env.BuildName = 'live'
    env.LastBuildRoot = '%(AppRoot)s/builds/live/latest' % env
    env.BuildRoot = '%(AppRoot)s/builds/live/%(tag)s' % env
    env.CacheDir = '/var/cache/grabagame'
    env.LogDir = '/var/log/grabagame'

    "Domain Settings"
    env.BaseUrl = 'http://grabagame.co.nz/'

    "Database settings"
    env.DatabaseName = 'Grabagame_live'
    env.DatabaseUser = 'Grabagame'
    env.DatabasePassword = 'ooge5viej3roo8shoo3voXeoquudaesh'

    "Other settings"
    env.Secret = 'Yeux3woo2aihi5ohrohtijup7fieCh7g'
