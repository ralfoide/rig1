#!/bin/bash

usage()
{
	echo
	echo "Usage: $0 cvs_tag"
	echo "where cvs_tag must be:"
	echo "  - \"today\""
	echo "  - a date in the form 'tag_YYYY-MM-DD_vVVVV' (f.ex tag_2002-11-04_v0623)"
	echo
	exit 1;
}

if [ "$1" == "" ]
then
	usage
fi

if [ "$1" != "today" ];
then
	RIG=`echo -n "$1" | sed 's/tag/rig/'`
	TAG="-r $1"
else
	RIG=`date +rig_%Y-%m-%d | tr -d "\n"`
	TAG=""

	if [ -f ./rig/src/version.php ];
	then
		A=`grep "\$rig_version = \"" ./rig/src/version.php | sed 's/.*"\([0-9i\.]*\)".*/\1/'`
		echo $A
	fi
	RIG="${RIG}_v${A}_tmp"
fi

echo
echo "########## Processing $RIG ################"
echo

# switch to the temp dir
mkdir -p /tmp/rig
pushd /tmp/rig

rm -rvf "$RIG"

echo
echo "########## CVS checkout $RIG ################"
echo

export CVS_RSH=ssh
export CVSROOT=:ext:ralfoide@cvs.rig-thumbnail.sourceforge.net:/cvsroot/rig-thumbnail

cvs -z3 checkout $TAG rig

# remove CVS dirs
find . -name "CVS" -exec rm -rfv '{}' ";"

echo
echo "########## Creating TGZ $RIG ################"
echo

mv -v rig "$RIG"
tar cvzf "$RIG.tgz" "$RIG"

# remove temp dir
rm -rf "$RIG"

echo
echo "########## Done creating $RIG ################"
echo

# backup any similar file here and move new archive file back here
popd
mv -v --backup=numbered "/tmp/rig/$RIG.tgz" .

echo
echo "Result file is in" `pwd`
ls -la "$RIG.tgz"


echo
