Setting up new Voting Mailing Application
    *Voting Mailing Application*
    Sends emails to users using gmail 
    authentication for use when starting
    a new poll in admin/vote.php
-----------------------------------------
1. Execute sql/votingSetup.sql
2. Update portsnap: freebsd.org/doc/en/books/handbook/ports-using.html
3. Enable openssl support: /usr/local/etc/php.ini
4a.cd /usr/ports/security/php5-openssl
    4b. make install clean 
