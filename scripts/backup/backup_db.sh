#!/bin/bash

# Define a timestamp function
timestamp() {
	date +%s
}

BACKUP_FILE=/home/[USERNAME]/asaam/backup_$(timestamp).sql

mysqldump asaam > ${BACKUP_FILE}
gzip ${BACKUP_FILE}
