WordPress Gitframe
==================

### Description
WordPress Gitframe is a directory framework used to build WordPress powered websites while maintaining the files using Git.

### Objective
The goal of this project is to separate WordPress core files from plugin, theme, uploaded and generated files. Basically, keeping the WordPress core directory as clean as possible, while being able to maintain plugins and themes using Git Submodules. This project also uses the practice of separating the wp-config.php file away from core, and using a separate config.php file to define all sensitive data, thus keeping your configuration out of your web root.

### Benefits
You're probably asking yourself, what are the benefits of such a seemingly complicated setup? In one word, control. The ability to completely control your WordPress setup. I also find that when working in this fashion, I'm more cautious about the plugins and themes installed, thus ending up with an overall leaner install.

### Assumptions/Requirements
*Some of these locations may have to be changed to fit your environment!*

- Repository (bare): `[root@host:/var/www/repo/website.git#]`
- Website (production): `[root@host:/var/www/html/website.com#]`
    - **IMPORTANT!** This requires you have a website set up on `production` with the root directory (where files are served from) in `/var/www/html/website.com/public_html`.
- Website (development): `[root@dev:/var/www/html/website.dev#]`
    - **IMPORTANT!** This requires you have a website set up on `development` with the root directory (where files are served from) in `/var/www/html/website.dev/public_html`.

#### Example VirtualHost

    #
    #  website.com (/etc/apache2/sites-available/www.website.com.conf)
    #
    <VirtualHost *:80>
        ServerAdmin     webmaster@localhost
        ServerName	website.com
        ServerAlias	www.website.com
        DocumentRoot	/var/www/html/website.com/public_html
        
        <Directory />
            Options FollowSymLinks
            AllowOverride None
        </Directory>
        <Directory /var/www/html/website.com/public_html/>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride None
                Order allow,deny
                allow from all
        </Directory>

        ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/
        <Directory "/usr/lib/cgi-bin">
                AllowOverride None
                Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
                Order allow,deny
                Allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.website.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/access.website.log combined
    </VirtualHost>

**Directory Structure**

When all is said and done, expect a directory structure similar to this (on your production machine):

    /var
    |   www/
    |   |   repo/
    |   |   |   website.git/
    |   |   |   |   hooks/
    |   |   |   |   info/
    |   |   |   |   objects/
    |   |   |   |   refs/
    |   |   |   |   config
    |   |   |   |   description
    |   |   |   |   HEAD
    |   |   html/
    |   |   |   website.com/
    |   |   |   |   .git/
    |   |   |   |   |   hooks/
    |   |   |   |   |   info/
    |   |   |   |   |   objects/
    |   |   |   |   |   refs/
    |   |   |   |   |   config
    |   |   |   |   |   description
    |   |   |   |   |   HEAD
    |   |   |   |   public_html/
    |   |   |   |   |   core/
    |   |   |   |   |   lib/
    |   |   |   |   |   |   plugins/
    |   |   |   |   |   |   |   install.php
    |   |   |   |   |   |   themes/
    |   |   |   |   |   |   uploads/
    |   |   |   |   |   var/
    |   |   |   |   |   |   backup/
    |   |   |   |   |   |   cache/
    |   |   |   |   |   index.php
    |   |   |   |   |   wp-config.php
    |   |   |   |   .gitignore
    |   |   |   |   .gitmodules
    |   |   |   |   config.php
    |   |   |   |   README.md

And like so on your development machine:

    /var
    |   www/
    |   |   html/
    |   |   |   website.dev/
    |   |   |   |   .git/
    |   |   |   |   |   hooks/
    |   |   |   |   |   info/
    |   |   |   |   |   objects/
    |   |   |   |   |   refs/
    |   |   |   |   |   config
    |   |   |   |   |   description
    |   |   |   |   |   HEAD
    |   |   |   |   public_html/
    |   |   |   |   |   core/
    |   |   |   |   |   lib/
    |   |   |   |   |   |   plugins/
    |   |   |   |   |   |   |   install.php
    |   |   |   |   |   |   themes/
    |   |   |   |   |   |   uploads/
    |   |   |   |   |   var/
    |   |   |   |   |   |   backup/
    |   |   |   |   |   |   cache/
    |   |   |   |   |   index.php
    |   |   |   |   |   wp-config.php
    |   |   |   |   .gitignore
    |   |   |   |   .gitmodules
    |   |   |   |   config.php
    |   |   |   |   README.md

### Quick FAQ

1. Where's WordPress?!? WordPress will be installed as a Git Submodule (located in `public_html/core`).
2. Where's wp-content?!? `wp-content` will be renamed, and reside outside of `public_html/core/wp-content` in `public_html/lib`.
3. `index.php` and `wp-config.php` will reside outside of `public_html/core` in `public_html`.
4. Uploads will reside outside of `public_html/core/wp-content/uploads` in `public_html/lib/uploads`. There is a required drop-in to make this work.
5. Alternative plugin data will reside in `public_html/var`.
6. Works with WordPress Multisite installations.

### Notice
- Once this setup is complete, you will have to add all plugins and themes manually (or as a git submodule).
- This setup only goes as far as getting WordPress installed; installing plugins and themes is beyond the scope of this project.
- This setup does not include staging environments. You are responsible for setting them up yourself.

### Quick Start
Simply clone this repository and initiate all submodules.

Complete Setup
==============
## 1. Create a bare repository on the server, outside of webroot.

    [root@host:~#] cd /var/www/repo && mkdir website.git
    [root@host:/var/www/repo#] cd website.git
    [root@host:/var/www/repo/website.git#] git init --bare
    
**a. Create the post-receive file:**

    [root@host:/var/www/repo/website.git#] cd hooks
    [root@host:/var/www/repo/website.git/hooks#] nano post-receive

    #!/bin/bash
    #this is for ssh purposes
    #read oldrev newrev refname

    #Where to store the log information about the updates
    LOGFILE=./post-receive.log

    #The deployed directory (the running site)
    DEPLOYDIR=../../../html/website.com

    while read oldrev newrev refname
    do
        branch=`echo $refname | cut -d/ -f3`

    if [ "master" == "$branch" ]; then
        #Recored the fact that the push has been received
        echo -e "-----------------------------------------------------" >> $LOGFILE
        echo -e "Received Push Request on $(date)" >> $LOGFILE
        echo " - Starting code update."
        echo " - Changes pushed to origin master."
        echo " - Old SHA: $oldrev" >> $LOGFILE
        echo " - New SHA: $newrev" >> $LOGFILE
        echo " - Ref Name: $refname" >> $LOGFILE
        echo " - Branch: $branch" >> $LOGFILE
        echo -e "-----------------------------------------------------" >> $LOGFILE
		
        echo " - Move to production environment; exit if failure"
        cd ../../../html/website.com/.git || exit
        echo " - unset GIT_DIR"
        unset GIT_DIR
        echo " - Changing directory to the working tree; exit if failure"
        cd `git config --get core.worktree` || exit
        echo " - Pulling origin"
        git pull origin
        echo " - Fetch commits and tags for each submodule"
        git submodule foreach git fetch --tags
        echo " - Updating submodules"
        git submodule update --init --recursive --force
		
        echo " - Finished code deployment"
    fi
    done
    
To save and exit, execute: `Crtl-X, Y, Enter`

**b. Make the post-reveive hook executable:**

`[root@host:/var/repo/website.git/hooks#] chmod +x post-receive`

At this point we've set up the repository that will house our website (website.git), and created a post-receive hook that will commit our webiste to the bare repository, change to our production directory, and pull our website from the bare repository.

Current Directory Structure (production):

    /var
    |   www/
    |   |   repo/
    |   |   |   website.git/
    |   |   |   |   hooks/
    |   |   |   |   |    post-receive
    |   |   |   |   info/
    |   |   |   |   objects/
    |   |   |   |   refs/
    |   |   |   |   config
    |   |   |   |   description
    |   |   |   |   HEAD

## 2. Clone the bare repository into our production environment (inside of webroot).

**a. Clone the bare repository:**

    [root@host:/var/www/repo/website.git/hooks#] cd /var/www/html && mkdir website.com
    [root@host:/var/www/html#] cd website.com
    [root@host:/var/www/html/website.com#] git clone ../../repo/website.git

Current Directory Structure (production):

    /var
    |   www/
    |   |   repo/
    |   |   |   website.git/
    |   |   |   |   hooks/
    |   |   |   |   |    post-receive
    |   |   |   |   info/
    |   |   |   |   objects/
    |   |   |   |   refs/
    |   |   |   |   config
    |   |   |   |   description
    |   |   |   |   HEAD
    |   |   html/
    |   |   |   website.com/
    |   |   |   |   .git/
    |   |   |   |   |    hooks/
    |   |   |   |   |    info/
    |   |   |   |   |    objects/
    |   |   |   |   |    refs/
    |   |   |   |   |    config
    |   |   |   |   |    description
    |   |   |   |   |    HEAD

**b. With the repository in place, lets modify a few parameters of this repository.**

    [root@host:/var/www/html/website.com#] git config --bool receive.denyCurrentBranch false && git config --path core.worktree ../

Excellent, now we're done with our bare repository and production directory. Let's start on our development environment. This assumes your development environment is on a different machine.

## 3. Clone the bare repository into your development directory.

    [root@dev:~#] cd /var/www/html
    [root@dev:/var/www/html#] git clone root@host:path/to/website.git website.dev

Perfect. At this point, we can start developing and pushing to our repository, which in turn will switch directories to the production website, and pull in those changes, including submodules.

## 4. Develop the installation.

    [root@dev:/var/www/html#] cd website.dev
    [root@dev:/var/www/html/website.dev#] touch readme.md
    [root@dev:/var/www/html/website.dev#] git add .
    [root@dev:/var/www/html/website.dev#] git commit -m "Initial commit."

Current Directory Structure (development):

    /var
    |   www/
    |   |   html/
    |   |   |   website.dev/
    |   |   |   |   .git/
    |   |   |   |   |   hooks/
    |   |   |   |   |   info/
    |   |   |   |   |   objects/
    |   |   |   |   |   refs/
    |   |   |   |   |   config
    |   |   |   |   |   description
    |   |   |   |   |   HEAD
    |   |   |   |   readme.md

(OPTIONAL) At this early stage, I like to add a .gitignore file.

    [root@dev:/var/www/html/website.dev#] curl -O https://gist.githubusercontent.com/salcode/b515f520d3f8207ecd04/raw/.gitignore
    
(OPTIONAL) Modify the .gitignore file to suite your environment. If you choose to track it, don't forget to add and commit it.

    [root@dev:/var/www/html/website.dev#] git add .gitignore
    [root@dev:/var/www/html/website.dev#] git commit -m "Added .gitignore."

Current Directory Structure (development):

    /var
    |   www/
    |   |   html/
    |   |   |   website.dev/
    |   |   |   |   .git/
    |   |   |   |   |   hooks/
    |   |   |   |   |   info/
    |   |   |   |   |   objects/
    |   |   |   |   |   refs/
    |   |   |   |   |   config
    |   |   |   |   |   description
    |   |   |   |   |   HEAD
    |   |   |   |   .gitignore
    |   |   |   |   readme.md

Now we can start adding our submodules. (The force parameter is necessary if you've used .gitignore to ignore the root directory.)

## 5. Add Git Submodule (WordPress)

    [root@dev:/var/www/html/website.dev#] git submodule add --force git://github.com/WordPress/WordPress.git public_html/core
    [root@dev:/var/www/html/website.dev#] git commit -m "Added WordPress submodule."
    [root@dev:/var/www/html/website.dev#] cd public_html/core
    [root@dev:/var/www/html/website.dev/public_html/core#] git checkout 4.7.4
    [root@dev:/var/www/html/website.dev/public_html/core#] cd ../../
    [root@dev:/var/www/html/website.dev#] git commit -am "Checkout WordPress 4.7.4"
    
Current Directory Structure (development):

    /var
    |   www/
    |   |   html/
    |   |   |   website.dev/
    |   |   |   |   .git/
    |   |   |   |   |   hooks/
    |   |   |   |   |   info/
    |   |   |   |   |   objects/
    |   |   |   |   |   refs/
    |   |   |   |   |   config
    |   |   |   |   |   description
    |   |   |   |   |   HEAD
    |   |   |   |   public_html/
    |   |   |   |   |   core/
    |   |   |   |   .gitignore
    |   |   |   |   .gitmodules
    |   |   |   |   readme.md

## 6. Move `wp-content`, `wp-config.php`, and `index.php`

    [root@dev:/var/www/html/website.dev#] cp -R public_html/core/wp-content public_html/lib
    [root@dev:/var/www/html/website.dev#] cp public_html/core/wp-config-sample.php public_html/wp-config.php
    [root@dev:/var/www/html/website.dev#] cp public_html/core/index.php public_html/index.php
    [root@dev:/var/www/html/website.dev#] git add -A
    [root@dev:/var/www/html/website.dev#] git commit -m "Move wp-content, wp-config.php, index.php to public_html."
    
Current Directory Structure (development):

    /var
    |   www/
    |   |   html/
    |   |   |   website.dev/
    |   |   |   |   .git/
    |   |   |   |   |   hooks/
    |   |   |   |   |   info/
    |   |   |   |   |   objects/
    |   |   |   |   |   refs/
    |   |   |   |   |   config
    |   |   |   |   |   description
    |   |   |   |   |   HEAD
    |   |   |   |   public_html/
    |   |   |   |   |   core/
    |   |   |   |   |   lib/
    |   |   |   |   |   |   plugins/
    |   |   |   |   |   |   themes/
    |   |   |   |   |   index.php
    |   |   |   |   |   wp-config.php
    |   |   |   |   .gitignore
    |   |   |   |   .gitmodules
    |   |   |   |   readme.md

At this point, we need to edit some files. In particular, we're going to separate our wp-config.php files for version control.

In `public_html/index.php`, modify: `require('./wp-blog-header.php');` to include the wordpress directory: `require('./core/wp-blog-header.php');`

We're going to replace `wp-config.php` with:

    <?php
    // =================================================================================
    // Load database info
    // =================================================================================
    if ( file_exists( dirname( __FILE__ ) . '/../config.php' ) ) {
        include( dirname( __FILE__ ) . '/../config.php' );
    }
    // =================================================================================
    // Bootstrap WordPress
    // =================================================================================
    if ( !defined( 'ABSPATH' ) )
        define( 'ABSPATH', dirname( __FILE__ ) . '/core/' );
    require_once( ABSPATH . 'wp-settings.php' );
    
This allows us to maintain this copy in git, while separating our database credentials. As you can see, it requires a file `config.php` from the `website.com` (or website.dev) directory. 

## 7. Create `config.php`

    <?php
    // =================================================================================
    // Define the type of server and load database info and local development parameters
    // =================================================================================
    if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') {
        define('WP_ENV', 'dev');
    } else {
        define('WP_ENV', 'prod');
    }
    if (WP_ENV == 'dev') {

        define('DB_NAME', 'database');                                                      // DB_NAME
        define('DB_USER', 'username');                                                      // DB_USER
        define('DB_PASSWORD', 'password');                                                  // DB_PASSWORD
        define('DB_HOST', 'localhost');                                                     // DB_HOST

        define('WP_DEBUG', true );                                                          // controls the reporting of errors and warnings
        define('WP_LOCAL_DEV', true );

    } else {

        define('DB_NAME', 'database');                                                      // DB_NAME
        define('DB_USER', 'username');                                                      // DB_USER
        define('DB_PASSWORD', 'password');                                                  // DB_PASSWORD
        define('DB_HOST', 'localhost');                                                     // DB_HOST

    }

    $table_prefix  = 'wp_';
    
    define( 'WPLANG', '' );

    define('DB_CHARSET', 'utf8');                                                           // DB_CHARSET
    define('DB_COLLATE', '');                                                               // DB_COLLATE

    define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/core');                    // where your WordPress core files reside.
    define('WP_HOME',    'http://' . $_SERVER['SERVER_NAME']);                              // where to reach your WordPress blog.
    define('WP_CONTENT_DIR', dirname(__FILE__) . '/public_html/lib' );                      // move the wp-content directory outside of the WordPress application directory.
    define('WP_CONTENT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/lib');                 // move the wp-content directory outside of the WordPress application directory.

    define('DISALLOW_FILE_EDIT', true );                                                    // disable the file editor.
    define('AUTOMATIC_UPDATER_DISABLED', true );                                            // disable automatic updates.
    define('DISALLOW_FILE_MODS', true );                                                    // disable the file editor, automatic updates, and installing plugins/themes from within WP.
    define('WP_POST_REVISIONS', false );                                                    // disable post revisions.

    if ( WP_DEBUG ) {
        define('WP_DEBUG_LOG', true );                                                      // stored in wp-content/debug.log
        define('WP_DEBUG_DISPLAY', true );
        define('SCRIPT_DEBUG', true );                                                      // if you are planning on modifying some of WP built-in JS or CSS
        define('SAVEQUERIES', true );
        @ini_set('display_errors', 0 );
    }

    // =================================================================================
    // Salts, for security
    // Grab these from: https://api.wordpress.org/secret-key/1.1/salt
    // =================================================================================
    define( 'AUTH_KEY',         'put your unique phrase here' );
    define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
    define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
    define( 'NONCE_KEY',        'put your unique phrase here' );
    define( 'AUTH_SALT',        'put your unique phrase here' );
    define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
    define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
    define( 'NONCE_SALT',       'put your unique phrase here' );

Commit your changes.    

    [root@dev:/var/www/html/website.dev#] git commit -am "Modified index.php, wp-config.php. Added config.php."

Current Directory Structure (development):

    /var
    |   www/
    |   |   html/
    |   |   |   website.dev/
    |   |   |   |   .git/
    |   |   |   |   |   hooks/
    |   |   |   |   |   info/
    |   |   |   |   |   objects/
    |   |   |   |   |   refs/
    |   |   |   |   |   config
    |   |   |   |   |   description
    |   |   |   |   |   HEAD
    |   |   |   |   public_html/
    |   |   |   |   |   core/
    |   |   |   |   |   lib/
    |   |   |   |   |   |   plugins/
    |   |   |   |   |   |   themes/
    |   |   |   |   |   index.php
    |   |   |   |   |   wp-config.php
    |   |   |   |   .gitignore
    |   |   |   |   .gitmodules
    |   |   |   |   config.php
    |   |   |   |   readme.md

Finally, we're going to create a drop-in plugin that will set our uploads path to our desired location (note that this file can do much, much more beyond the scope of this project).

## 8. Create `public_html/lib/install.php`

    <?php

    /*
       Plugin Name: Custom Installation Script
    */

    !defined( 'ABSPATH' ) AND exit;
    
    
    // Fix media directory location.
        if ( empty( $upload_path ) || 'wp-content/uploads' == $upload_path ) {
            update_option( 'upload_path', untrailingslashit( str_replace( 'core', 'lib\uploads', ABSPATH ) ) );
            update_option( 'upload_url_path', home_url( '/lib/uploads' ) );
        }
    }

Commit your changes.    

    [root@dev:/var/www/html/website.dev#] git commit -am "Added install.php."

Current Directory Structure (development):

    /var
    |   www/
    |   |   html/
    |   |   |   website.dev/
    |   |   |   |   .git/
    |   |   |   |   |   hooks/
    |   |   |   |   |   info/
    |   |   |   |   |   objects/
    |   |   |   |   |   refs/
    |   |   |   |   |   config
    |   |   |   |   |   description
    |   |   |   |   |   HEAD
    |   |   |   |   public_html/
    |   |   |   |   |   core/
    |   |   |   |   |   lib/
    |   |   |   |   |   |   plugins/
    |   |   |   |   |   |   themes/
    |   |   |   |   |   |   install.php
    |   |   |   |   |   index.php
    |   |   |   |   |   wp-config.php
    |   |   |   |   .gitignore
    |   |   |   |   .gitmodules
    |   |   |   |   config.php
    |   |   |   |   readme.md

## 9. Install WordPress

You can now navigate to your webroot and install WordPress. If you haven't already, push to origin.

## 10. Updating Submodules

To update submodules, execute the following:

    [root@dev:/var/www/html/website.dev#] cd public_html/core
    [root@dev:/var/www/html/website.dev/public_html/core#] git fetch --tags
    [root@dev:/var/www/html/website.dev/public_html/core#] git checkout 4.7.4
    [root@dev:/var/www/html/website.dev/public_html/core#] cd ../../
    [root@dev:/var/www/html/website.dev#] git add public_html/core
    [root@dev:/var/www/html/website.dev#] git commit -am "Updated WordPress from 4.7.3 to 4.7.4"
    [root@dev:/var/www/html/website.dev#] git push origin --all

    All in One: cd public_html/core && git fetch --tags && git checkout 4.7.4 && cd ../../ && git add public_html && git commit -am "Updated WordPress from 4.7.3 to 4.7.4" && git push origin --all