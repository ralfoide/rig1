#!/bin/bash

usage()
{
	echo
	echo "Usage: $0 A.tgz B.tgz"
	echo "Filenames *must* be in form rig_YYYY-MM-DD_v1234.tgz"
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

# get filename component

FA=`basename "$1"`
FB=`basename "$2"`

# remove extension

DA=${FA/.tgz/}
DB=${FB/.tgz/}

if [ "${DA}.tgz" != "$FA" ]
then
	echo "File $FA does not end with .tgz"
	usage
fi

if [ "${DB}.tgz" != "$FB" ]
then
    echo "File $FB does not end with .tgz"
    usage
fi

# get version number

VA=${DA/rig_[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]_/}
VB=${DB/rig_[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]_/}

# get date from filename

TA=${DA/rig_/} ; TA=${TA/_v[0-9]*/}
TB=${DB/rig_/} ; TB=${TB/_v[0-9]*/}

if [ "rig_${TA}_${VA}" != "$DA" ]
then
	echo "Error: $DA is not in the form rig_YYYY-MM-DD_v1234"
	usage
fi

if [ "rig_${TB}_${VB}" != "$DB" ]
then
	echo "Error: $DB is not in the form rig_YYYY-MM-DD_v1234"
	usage
fi



echo
echo "########## Extracting $DA / $DB ################"
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

DEST="rig_${TB}_${VA}-${VB}_diff.txt"
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

