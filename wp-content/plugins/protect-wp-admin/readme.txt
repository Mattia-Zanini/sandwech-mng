=== Protect WP Admin ===
Contributors: wpexpertsin, india-web-developer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZEMSYQUZRUK6A
Tags: secure website, secure wordpress, protect wp admin, protection,security, wordpress protection, wordpress security, prevent hacking, hack, secure login, website security, change username, rename username, admin url, secure admin, username, protect admin, login, secure wordpress admin, admin login, admin, rename admin url
Requires at least: 5.3
Tested up to: 6.0.2
Stable tag: 3.8

== Description ==

WP Protect Admin Plugin has Provide Extra Secutiry Layer to Protect Your WordPress Admin Area. Using this plugin you can safe your site using necessary features like change default admin login url (/wp-admin) user name & login history log.

If you run a WordPress website, you should absolutely use "protect-wp-admin" to secure it against hackers.

Protect WP-Admin fixes a glaring security hole in the WordPress community: the well-known problem of the admin panel URL.

Protect WP-Admin solves this problem by allowing administrators to customize their admin panel URL and blocking the default links.

Administrators will be able to change default login page url "sitename.com/wp-admin" to something like "sitename.com/custom-string", so after that guest user will be redirected to the homepage.

The plugin also comes with some access filters, allowing Administrator to restrict guest and registered users access to wp-admin, just in case you want some of your editors to log in the classic way.


https://youtu.be/Mxr2MLDNACE

 **[ Click here to download add-on ](https://www.wp-experts.in/products/protect-wp-admin-pro)**


= Features =

 * Define Custom WP Admin Login URL (i.e http://yourdomain.com/myadmin)
 * Define Logo Image for Login Page
 * Define Background Color for Login Page
 * Define Text Color for Login Page
 * Restrict Guest Users to Access Admin Dashboard
 * Restrict Registered Non-Admin Users to Acces Admin Dashboard
 * Allow Admin Dashboard Access Bt Defining Comma Separated Multiple Ids


### FLAT 15% DISCOUNT ON !! No Coupon Code Required. Hurry! Limited Time Offer!
 **[ Click here to download add-on](https://www.wp-experts.in/products/protect-wp-admin-pro/?utm_source=wordpress.org&utm_medium=free-plugin&utm_campaign=15off)**

= Add-on Features =

We have also released an add-on for Protect-WP-Admin which not only demonstrates the flexibility of Protect-WP-Admin, but also adds some important features

 * Rename wordpress wp-admin URL.
 * Enable Login Tracker.
 * Allow Number of Login Attempt.
 * Change Username of any Existing Users.
 * Define Login Page Logo URL.
 * Manage Login Page Style From Admin.
 * Define Custom Redirect URL for Default wp-admin URL.
 * Track User Login History.
 * Faster Support.

 **[ Click here to download add-on](https://www.wp-experts.in/products/protect-wp-admin-pro/?utm_source=wordpress.org&utm_medium=free-plugin&utm_campaign=15off)**

 Do You Have Any Query? **[Submit here](https://www.wp-experts.in/contact-us/?utm_source=wordpress.org&utm_medium=free-plugin&utm_campaign=protect-admin)**

https://youtu.be/sXywBe0XWy0


== Installation ==
In most cases you can install automatically from WordPress.

However, if you install this manually, follow these steps:

 * Step 1. Upload "protect-wp-admin-pro" folder to the `/wp-content/plugins/` directory
 * Step 2. Activate the plugin through the Plugins menu in WordPress
 * Step 3. Go to Settings "Protect WP-Admin Pro" and configure the plugin settings.

== Frequently Asked Questions ==

#1.) Nothing happens after enabling and adding the new wordpress admin url? 

Don't worry, Just update the site permalink ("Settings" >> "Permalinks") and re-check,Now this time it will be work fine

#2.) Not able to login into admin after enabling the plugin? 

The issue can come when you do not give proper writable permission on htaccess file OR you have not update permalink settings to SEO friendly url from admin. You can access the login page url with default wp-admin slug after disable my plugin, you can disable plugin through FTP by rename protect-wp-admin folder to any other one. 

#3.) I am not able to login after installation

Basically issues can come only in case when you will use default permalink settings. 
If your permalink will be updated to any other option except default (Plain) then it will work fine. Feel free to contact us over email raghunath.0087@gmail.com for any query

#4 Can I change the username of existing users ?
Yes, You can change the username of your existing users from admin, This feature is available in our addon.

#5 Can I set a login attempt count for unsuccessful attempts?
Yes, You can set the number of unsuccessful attempts, This feature is available in our addon.

#6 Can I access the login history of users from admin using this plugin?
Yes, You can see the login history from the plugin settings page. This feature is available in our addon.

#7 Is there any option to set the number of login attempts?
* Yes, there is an option in pro addon not in free version. please browse https://www.wp-experts.in/products/protect-wp-admin-pro this url for purchase to pro addon.

#8 Getting 404 page with a new admin url, How I can fix it?
404 issue can come due to htaccess permission issue, You can use below sample code to add it manually in site root htaccess file

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteRule ^myadmin/?$ /wp-login.php [QSA,L]
RewriteRule ^myadmin/register/?$ /wp-login.php?action=register [QSA,L]
RewriteRule ^myadmin/lostpassword/?$ /wp-login.php?action=lostpassword [QSA,L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
 
Here we are using new admin slug as "myadmin" so as per your new admin slug you will require update this value 

== Screenshots ==

1. screenshot-1.png

2. screenshot-2.png

3. screenshot-3.png

4. screenshot-4.png

5. screenshot-5.png



== Changelog == 

= 3.8 = 
 * Updated security in code
 * Tested with wordpress version 5.9.3
 
= 3.7 = 
 * fixed url issue
 * optimized code
 
= 3.6 = 
 * Optimized the code and security things
 * Tested with wordpress version 5.8.2

= 3.5 = 
 * Tested with new wordpress version 5.8
 * Fixed double slash url access issue wp-login

= 3.4 = 
 * Tested with new wordpress version 5.5.1
 * added condition to show admin menu bar only for admin 

= 3.3 = 
 * Tested with new wordpress version 5.4.2
 * Fixed new admin url page not found issue when you setup wordpress in subdirectory. 
 * Optimized code 
 * Released add-on new version 2.0

= 3.2 = 
 * Tested with new wordpress version 5.4.1
 * Fixed notice error

= 3.1 = 
 * Tested with new wordpress version 5.4
 * Added text color option
 * Fixed DOING_AJAX notice error
 * Released addon new version 1.9
 
= 3.0.3 = 
 * Tested with new wordpress version 5.0.2
 * Fixed some minor issues
 
= 3.0.2 = 
 * Tested with new wordpress version 4.9.8
 * Fixed some minor issues
 
= 3.0.1 = 
 * Fixed admin access issues 

= 3.0 = 
 * Tested with new wordpress version 4.9.7
 * Optimized code of the plugin 
 
= 2.9 = 
 * Tested with new wordpress version 4.9.4
 * Fixed getimagesize() function issue for HTTPS urls 
 
= 2.8 = 
 * Tested with new wordpress version 4.8.1
 * Added upload image lightbox and color picker
 
= 2.7 = 
 * Tested with new wordpress version 4.8.1
 * Added upload image lightbox and color picker
 
= 2.6 = 
 * Tested with new wordpress version 4.8
 
= 2.5 = 
 * Fixed links issues on login, forget and register page for all language
 * Fixed access the wp login page using new admin slug even admin is already logged in

= 2.4 = 
 * Tested with new wordpress version 4.7
 * Fixed images logo image notice error issue.
 
= 2.3 = 
 * Tested with new wordpress version 4.6.1
 * Fixed images size logo issue
 * Modify code for redirect user to new admin url
= 2.2 = 
 * Tested with new wordpress version 4.5.3
 * Optimized plugin code
= 2.1 = 
 * Tested with new wordpress version 4.5.2
 
= 2.0 = 
 * Tested with new wordpress version 4.5 
 * Removed localhost permission related conditions.

= 1.9 = 
 * Fixed htaccess writable notice popup related issues on localhost 
 * Add an new confirmation alert before enable plugin 

= 1.8 = 
 * Fixed Login Failure issue
 * Released Pro Addon

= 1.7 = 
 * Fixed forget password email issue
 * Fixed forgot password link issue on login error page

= 1.6 = 
 *  Fixed wp-login.php issue for www url
 
= 1.5 = 
 * Fixed wp-login url issue
 * Fixed wp-admin url issue

= 1.4 = 
 * Fixed links issue on "Register", "Login" & "Lost Password" As Per New Admin Url
 * Fixed the "Register", "Login" & "Lost Password" Form Action URL As Per New Admin Url
 * Add validation to check SEO friendly url enable or not.
 * Add validation to check whether the .htaccess file is writable or not.

= 1.3 = 
 * Added an option for define to admin login page logo
 * Added an option for define to wp-login page background-color
 * Fixed some minor css issues

= 1.2 = 
 * Added new option for allow admin access to non-admin users
 * Added condition for check permalink is updated or not
 * Fixed a minor issues (logout issues after add/update admin new url)
 
= 1.1 = 
 * Add new option for restrict registered users from wp-admin
 * Fixed permalink update issue after add/update admin new url. Now no need to update your permalink
 * Add option for redirect user to new admin url after update the new admin url

= 1.0 = 
 * First stable release
