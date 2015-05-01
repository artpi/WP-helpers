#!/bin/bash

DEV_DB_USER=""
DEV_DB_PASS=""
DEV_DB=""
DEV_DB_PREFIX=""
DEV_URL=""


TEST_DB_USER=""
TEST_DB_PASS=""
TEST_DB=""
TEST_DB_PREFIX=""
TEST_URL=""


function deploy {
    mysqldump -u $DEV_DB_USER $DEV_DB_PASS $DEV_DB --skip-comments --ignore-table=${DEV_DB}.${DEV_DB_PREFIX}users --ignore-table=${DEV_DB}.${DEV_DB_PREFIX}usermeta  | sed "s/\`${DEV_DB_PREFIX}/\`${TEST_DB_PREFIX}/g" | sed "s|$DEV_URL|$TEST_URL|g" | sed "s| COLLATE utf8mb4_unicode_ci||g" > ./DB.sql
    git add ./DB.sql
    git commit -m "New database settings"
    git push
}

function import {
    git pull
    git checkout test_env
    mysqldump -u $TEST_DB_USER $TEST_DB_PASS $TEST_DB  --skip-comments > ./DB-test.sql
    git add ./DB-test.sql
    git commit -m "Test env database backup"
    git push
    git checkout master
    mysql -u $TEST_DB_USER $TEST_DB_PASS $TEST_DB < ./DB.sql
}


case $1 in
  "deploy") deploy ;;
  "import") import ;;
  *) echo "Please use either deploy or import";;
esac