# Public repo for the imageboard
This was originally made to run on apache behind nginx. Admin login was handled by basic authentication in nginx at the path /login. When logged in, the user can see a delete button next to threads and replies.

### There are some environment variables to fill in
```
Database authentication strings:
DB_HOST
DB_USER
DB_PASS
DB_NAME

Site related:
SITE_URL (full https url to the website)
SITE_ICON (full https url to favicon and embed icon)
ADMIN_USERNAME 
SITE_NAME (i.e. my awesome imageboard)
```
Database (mariadb) is to be initialized with `init.sql`