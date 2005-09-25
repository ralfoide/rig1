#!/bin/bash

usage()
{
	echo
	echo "Usage: [-f] $0 A.tgz B.tgz"
	echo "Filenames *must* be in form rig_YYYY-MM-DD_v1234.tgz with A <= B."
	echo "Use -f to force the diff (in the case A<B is not correctly detected.)"
	echo
	exit 1;
}

if [ "$1" == "" ]
then
	usage
fi

FORCE=0
if [ "x$1" == "x-f" ]
then
	FORCE=1
	shift
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
	echo
	echo "File $FA does not end with .tgz"
	usage
fi

if [ "${DB}.tgz" != "$FB" ]
then
	echo
    echo "File $FB does not end with .tgz"
    usage
fi

# get version number

VA=${DA/rig_[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]_/}
VB=${DB/rig_[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]_/}
VA2=${VA/v/}
VB2=${VB/v/}
VA2=${VA2/_tmp/}
VB2=${VB2/_tmp/}

# get date from filename

TA=${DA/rig_/} ; TA=${TA/_v[0-9]*/}
TB=${DB/rig_/} ; TB=${TB/_v[0-9]*/}

# check versions & dates

if [ "$VA2" -gt "$VB2" ]
then
	echo
	if [ "$FORCE" == "1" ]
	then
		echo "Warning: $VA2 is greater than $VB2. Check ignored. Continuing."
	else
		echo "Error: $VA2 is greater than $VB2. Are you sure? (use -f to override)"
		usage
	fi
fi

if [ "rig_${TA}_${VA}" != "$DA" ]
then
	echo
	echo "Error: $DA is not in the form rig_YYYY-MM-DD_v1234"
	usage
fi

if [ "rig_${TB}_${VB}" != "$DB" ]
then
	echo
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

# diff options:
# u: universal diff
# r: recursive
# N: treat missing files as empty (both in source and dest)

DEST="rig_${TB}_${VA}-${VB}_diff.txt"
diff -urN "$DA" "$DB" > "$DEST"

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

