#!/bin/sh

URL=https://rig-thumbnail.svn.sourceforge.net/svnroot/rig-thumbnail

# V="./rig/src/version.php"

# keep only the directory portion of the calling program name
D="$0"
if [ -L "$D" ];
then
	# dereference link
	D=`ls -l "$D" | sed 's/^.*-> //'`
fi
D=${D/`basename $D`/}

V1="./rig/src/version.php"
V=V1
if [ ! -f $V ];
then
	V="$D/$V1"
fi;


if [ -f $V ];
then
	VERS=`grep "\$rig_version = \"" $V | sed 's/.*"\([0-9i\.]*\)".*/\1/' | sed 's/\.//g'`
else
	VERS="tag_YYYY-MM-DD_v1234"
fi

DATE=`date +%Y-%m-%d | tr -d "\n"`

TAG="tag_${DATE}_v$VERS"

if [ "$1" == "" ]
then
	echo
	echo "Usage: $0 <tag>"
	echo
	echo "where tag is in the form $TAG"
	echo
	exit 1
fi

# Only create the tag if there's no tag dir already existing
svn info $URL/tags/$TAG 2>/dev/null
RETVAL=$?
if [ "$RETVAL" == "0" ];
then
	echo
	echo "Error: tags/$TAG already exist. To recreate a tag remove the existing tag first:"
	echo "  svn rm $URL/tags/$TAG"
	echo
	exit 1
fi

svn copy $URL/trunk $URL/tags/$TAG

