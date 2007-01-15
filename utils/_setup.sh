#!/bin/sh
export CVS_RSH=ssh


# RM 20060624 SF.net once again changed the CVS host name... would be
# nice if they could just stop changing their mind.
#
# cf /etc/hosts to map the correct name.

export CVSROOT=:ext:ralfoide@rig-thumbnail.cvs.sourceforge.net:/cvsroot/rig-thumbnail
# export CVSROOT=:ext:ralfoide@cvs.rig-thumbnail.sourceforge.net:/cvsroot/rig-thumbnail
# export CVSROOT=:ext:ralfoide@cvs.sourceforge.net:/cvsroot/rig-thumbnail

export S=https://rig-thumbnail.svn.sourceforge.net/svnroot/rig-thumbnail

echo "Using CVS_RSH=$CVS_RSH and CVSROOT=$CVSROOT"
echo "S=$S"
echo
echo "Reminder: For sourceforge use 'svn commit --username ralfoide' and sourceforge's password"
