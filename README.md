## A simple blog api
This is a simple blog api written in laravel and has following features:

- Only authenticated users can see posts.
- Users with odd id cannot create posts.
- Users with even id can create posts.
- Only writer of a post can delete or update that post, unless the user is super admin.
- To define super admins add their emails comma separated in env file by SUPER_ADMIN_EMAILS key.
