#!/bin/bash

usage()
{
	echo
	echo "Usage: $0 A.tgz B.tgz"
	echo "Filenames *must* end with .tgz"
	echo
	exit 1;
}

if [ "$1" == "" ]
then
	usage
fi

if [ ! -f "$1" ]
then
echo "File $1 does not exist"
	usage
fi

if [ ! -f "$2" ]
then
	echo "File $2 does not exist"
	usage
fi

FA=`basename "$1"`
FB=`basename "$2"`

DA=${FA/.tgz/}
DB=${FB/.tgz/}

if [ "$DA" == "" ]
then
	echo "File $1 does not end with .tgz"
	usage
fi

if [ "$DB" == "" ]
then
    echo "File $2 does not end with .tgz"
    usage
fi
		

echo
echo "########## Extracting $DA / $D2 ################"
echo

# create the temp dir

DIR=/tmp/rig-diff
rm -rf $DIR
mkdir -p $DIR

# extract the files

zcat "$1" | (cd $DIR ; tar xvf - )
zcat "$2" | (cd $DIR ; tar xvf - )


echo
echo "########## Create diff ################"
echo

pushd $DIR > /dev/null

DEST="${DB}-diff.txt"
diff -ur "$DA" "$DB" > "$DEST"

if [ -e "$DEST" ]
then
	gzip -9 "$DEST"
	DEST="$DEST.gz"
fi

# cleanup
rm -rf "$DA"
rm -rf "$DB"

popd > /dev/null

echo
echo "########## Done creating diff ################"
echo

# backup any similar file here and move new diff file back here
mv -v --backup=numbered "$DIR/$DEST" .

echo
echo "Result file is in" `pwd`
ls -la "$DEST"

echo

