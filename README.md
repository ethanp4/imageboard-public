# Public repo for the imageboard
This was originally made to run on apache behind nginx. Admin login was handled by basic authentication in nginx at the path /login. When logged in the user can see a delete button next to threads and replies.

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

## Screenshots
<img width="1029" height="1136" alt="image" src="https://github.com/user-attachments/assets/43f841c2-fb06-4420-9112-c47335f99d77" />
<img width="736" height="631" alt="image" src="https://github.com/user-attachments/assets/4221db24-40d8-423c-8632-36d46f96c7ca" />
<img width="997" height="1155" alt="image" src="https://github.com/user-attachments/assets/880d76e2-1d9b-4730-b25d-6c6c72f053ef" />

