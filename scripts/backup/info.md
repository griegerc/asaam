# Daily backup
The shell script "backup_db.sh" makes a dump of a specific MySQL database, 
saves it as a MySQL dump and zips this file.

To run it as cronjob every day (e.g. at 3pm) you have to add the following 
configuration line into crontab (make sure all folders are setup):

    0 3 * * * /home/[USERNAME]/backup_db.sh

# Scheduled backup copy to local machine (Windows 10 with WinSCP)
To safe backup data to the local windows machine you have to install WinSCP 
and create an scheduled task in Windows:

- Go to Control Panel
- In Control Panel, go to System and Security > Administrative Tools > Schedule Tasks.
- In the Task Scheduler menu go to Action > Create Basic Task.
- Give your task a name and click Next.
- Choose when the task should be run and click Next.
- For task action, select Start a program and click Next.
- Browse for WinSCP.exe executable.
- In Add arguments add appropriate WinSCP command-line parameters:
    `/log=E:\asaam_backup\winscp.log /command "open sftp://[USERNAME]:[PASSWORD]@[DOMAIN]/" "get -delete /home/[USERNAME]/asaam/backup* E:\asaam_backup\" "exit"`
- When done, click Next, review your options and confirm with Finish.
