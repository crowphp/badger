#!/bin/sh

# --batch to prevent interactive command
# --yes to assume "yes" for questions
BASEDIR=$(dirname "$0")
gpg --quiet --batch --yes --decrypt --passphrase="$BADGER_ENCRYPTION_KEY" \
--output $BASEDIR/id_badger $BASEDIR/id_badger.gpg
gpg --quiet --batch --yes --decrypt --passphrase="$BADGER_ENCRYPTION_KEY" \
--output $BASEDIR/id_badger.pub $BASEDIR/id_badger.pub.gpg