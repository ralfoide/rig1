#!/bin/bash

. _setup.sh > /dev/null
URL="$S"
HTTP="${S/https/http}"
WEB="${HTTP/svnroot/viewvc}"

function die() {
  echo $1
  exit 1
}

[ $URL ] || die "_setup failed. No SVN URL defined"

if [ "$1" == "" ]
then
	echo
	echo "Usage: $0 <tag>"
	echo

	echo "Recent tags":
	svn ls $URL/tags | tail -n 5| sed 's@/@@'
	echo
	exit 1
fi

TAG="$1"

function change_version() {
  sed "s@^\(\$rig_version = \"[0-9.]*\).*\(\";\).*@\1 (<a href='$WEB/tags/$TAG'>$TAG</a>)\2@" version.php > version.php.tmp && rm version.php && mv version.php.tmp version.php && echo "Updated version.php"
}

echo
echo "####---- Checkout tag $TAG -----"
svn co $URL/tags/$TAG rig-stable-$TAG

echo
echo "####---- Create symlink -----"
ln -sfv rig-stable-$TAG/rig rig-stable

echo
echo"####---- Build thumbnail.exe -----"
(cd rig-stable/rig/thumbnail && make with-video)

echo
echo "####---- Update version ----"
(cd rig-stable/rig/src && change_version)

