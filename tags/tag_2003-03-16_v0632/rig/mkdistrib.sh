#!/bin/bash

usage()
{
	echo
	echo "Usage: $0 cvs_tag"
	echo "where cvs_tag must be in the form 'tag_YYYY-MM-DD_vVVVV' (f.ex tag_2002-11-04_v0623)"
	echo
	exit 1;
}

if [ "$1" == "" ]
then
	usage
fi

TAG="$1"
RIG=`echo -n "$TAG" | sed 's/tag/rig/'`

echo
echo "########## Processing $TAG ==> $RIG ################"
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

cvs -z3 checkout -r "$TAG" rig

# remove CVS dirs
find "$RIG" -name "CVS" -exec rm -rfv '{}' ";"

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
