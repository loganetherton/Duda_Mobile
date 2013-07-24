#!/bin/sh
#############################################################################################
# Duda Mobile cPanel Plugin Version : 1.0 Install file 
# Author: Logan Etherton
#
# Usage:
# ./dudamobileinstall.sh
#
# If you are unable to run this script via ./dudamobileinstall.sh, make sure its 
# permissions are set correctly. This can be done by typing the following:
# 
# chmod 0755 dudamobileinstall.sh
#
#############################################################################################

echo ""
echo "############################################################"
echo "#                   Installing Files                       #"
echo "############################################################"
echo ""

# Make sure the install file is in the same directory as the tarball expansion
if [ ! -e "dudamobileinstall.sh" ]; then
	echo "You must cd to the directory where you expanded the Duda Mobile plugin tarball."
	exit
fi

# I don't believe I'll need Perl support. It's here, commented out, just in case.
#if [ -e "/usr/local/cpanel/3rdparty/bin/perl" ]; then
#	find ./ -type f -exec sed -i 's%^#\!/usr/bin/perl%#\!/usr/local/cpanel/3rdparty/bin/perl%' {} \;
#fi

# Copy the contents of DudaMobile to the cPanel 3rd party directory
cp -r DudaMobile/ /usr/local/cpanel/base/3rdparty/ >/dev/null 2>&1
# Move the DudaMobile cPanel Plugin to where the it can be registered
cp -fv dudamobile.cpanelplugin /usr/local/cpanel/bin/ >/dev/null 2>&1
# Move DudaMobile WHM directory WHM docroot
cp -r whm/dudamobile/ /usr/local/cpanel/whostmgr/docroot/ >/dev/null 2>&1
# Create the dudamobile cgi directory and set permissions
mkdir /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile
chmod 700 /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile
# Move DudaMobile cgi script to where it can be found by the interpreter, and set permissions
cp -fv whm/dudamobile.cgi /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile >/dev/null 2>&1
chmod -v 700 /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile/dudamobile.cgi >/dev/null 2>&1
cp -avf dudamobile_version.txt /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile/dudamobile_version.txt
# Put other files into directory accessible by cgi interpreter
cp -avf dudamobile/ /usr/local/cpanel/whostmgr/docroot/cgi/
# Move the DudaMobile logo to cPanel icon for display in WHM
cp -fv whm/dudalogo.png /usr/local/cpanel/whostmgr/docroot/themes/x/icons/ >/dev/null 2>&1
# Move the DudaMobile WHM config file to where it can be accessed by AppConfig
cp -avf dudamobile.conf /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile/dudamobile.conf >/dev/null 2>&1

# Make sure that the base files were copied properly
if [ -f /usr/local/cpanel/base/3rdparty/DudaMobile/index.html ]; then
	echo "20% COMPLETE"
else
	echo "ERROR: Duda Mobile folder was unable to be copied. Installation Failed"
	exit 1
fi

sleep 1;

# Make sure that the WHM files were copied properly
if [ -f /usr/local/cpanel/whostmgr/docroot/dudamobile/index.html ]; then
	echo "40% COMPLETE"
else
	echo "ERROR: WHM files were unable to be failed. Installation failed."
	exit 1
fi

sleep 1;

# Make sure that the .cpanelplugin file was copied properly
if [ -f /usr/local/cpanel/bin/dudamobile.cpanelplugin ]; then
	echo "60% COMPLETE"
else
	echo "ERROR: dudamobile.cpanelplugin was unable to be copied. Installation failed."
	exit 1
fi

sleep 1;

# Make sure the cgi script was copied properly
if [ -f /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile/dudamobile.cgi ]; then
	echo "80% COMPLETE"
else
	echo "ERROR: dudamobile.cgi was unable to be copied. Installation failed."
	exit 1
fi

# Make sure that theme files were copied properly
if [ -f /usr/local/cpanel/whostmgr/docroot/themes/x/icons/dudalogo.png ]; then
	echo "100% COMPLETE"
else
	echo "ERROR: Theme files were unable to be copied. Installation failed."
	exit 1
fi

sleep 1;

# Once copying has finished, let the user know.
echo ""
echo "############################################################"
echo "#              File Structure Setup Complete               #"
echo "############################################################"
echo ""

# Check if WHM AppConfig is available for use
if [ -e "/usr/local/cpanel/bin/register_appconfig" ]; then
        # If AppConfig is available, run the DudaMobile config file and delete the cgi script
    	echo ""
	echo "############################################################"
	echo "#              WHM Installation via AppConfig              #"
	echo "############################################################"
	echo ""
        # Run appconfig on dudamobile.conf
        /usr/local/cpanel/bin/register_appconfig /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile/dudamobile.conf
        # Delete cgi files and directory
        /bin/rm -f /usr/local/cpanel/whostmgr/docroot/cgi//dudamobiledudamobile.cgi
        /bin/rm -Rf /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile
        /bin/rm -f /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile/dudamobile_version.txt
else
        # If AppConfig is not available, use the cgi script and delete the AppConfig file
	echo ""
	echo "############################################################"
	echo "#              WHM Installation via cgi                    #"
	echo "############################################################"
	echo ""
        if [ ! -d "/var/cpanel/apps" ]; then
            mkdir /var/cpanel/apps
            chmod 755 /var/cpanel/apps
        fi
        # Remove AppConfig file
        /bin/rm -f /usr/local/cpanel/whostmgr/docroot/cgi/dudamobile/dudamobile.conf
fi

echo ""
echo "############################################################"
echo "#                 Installing Theme Files                   #"
echo "############################################################"
echo ""

sleep 1;

# Check for x3. If detected, install theme files.
if [ -f /usr/local/cpanel/base/frontend/x3/index.html ]; then
	cp -r theme/dudamobile/ /usr/local/cpanel/base/frontend/x3/ >/dev/null 2>&1
	sleep 1;
        # Verify installation.
	if [ -f /usr/local/cpanel/base/frontend/x3/dudamobile/index.html ]; then
		echo "Installed support for x3 theme."
        # If x3 files could not be copied, fail with error message.
	else
		echo "ERROR: x3 theme detected, but theme files were unable to be copied. Installation failed."
		exit 1
	fi	
fi

sleep 1;

# Check for x3mail. If detected, install theme files.
if [ -f /usr/local/cpanel/base/frontend/x3mail/index.html ]; then
	cp -r theme/dudamobile/ /usr/local/cpanel/base/frontend/x3mail/ >/dev/null 2>&1
	sleep 1;
        # Verify installation.
	if [ -f /usr/local/cpanel/base/frontend/x3mail/dudamobile/index.html ]; then
		echo "Installed support for x3mail theme."
        # If x3mail files could not be copied, fail with error message.
	else
		echo "ERROR: x3 theme detected, but theme files were unable to be copied. Installation failed."
		exit 1
	fi	
fi

sleep 1;

# I'll add support for CleanPanel, RVSkin, and RVSkinLight if there's time

#if [ -f /usr/local/cpanel/base/frontend/CleanPanel/index.html ]; then
#	cp -r theme/dudamobile/ /usr/local/cpanel/base/frontend/CleanPanel/ >/dev/null 2>&1
#	sleep 1;
#	if [ -f /usr/local/cpanel/base/frontend/CleanPanel/dudamobile/index.html ]; then
#		echo "Installed support for CleanPanel theme."
#	else
#		echo "ERROR: CleanPanel detected, but theme files were unable to be copied. Installation failed."
#		exit 1
#	fi
#fi
#
#sleep 1;
#
#if [ -f /usr/local/cpanel/base/frontend/rvskin/index.html ]; then
#	cp -r theme/dudamobile/ /usr/local/cpanel/base/frontend/rvskin/ >/dev/null 2>&1
#	sleep 1;
#	if [ -f /usr/local/cpanel/base/frontend/rvskin/dudamobile/index.html ]; then
#		echo "Installed support for RVSkin theme."
#	else
#		echo "ERROR: RVSkin detected, but theme files were unable to be copied. Installation failed."
#		exit 1
#	fi
#fi
#
#sleep 1;
#
#if [ -f /usr/local/cpanel/base/frontend/rvskinlight/index.html ]; then
#	cp -r theme/dudamobile/ /usr/local/cpanel/base/frontend/rvskinlight/ >/dev/null 2>&1
#	sleep 1;
#	if [ -f /usr/local/cpanel/base/frontend/rvskinlight/dudamobile/index.html ]; then
#		echo "Installed support for RVSkinLight theme."
#	else
#		echo "ERROR: RVSkinLight detected, but theme files were unable to be copied. Installation Failed."
#		exit 1
#	fi
#fi
#
#sleep 1;

echo ""
echo "############################################################"
echo "#              Theme File Installation Completed           #"
echo "############################################################"
echo ""

sleep 1;

echo ""
echo "############################################################"
echo "#              Updating cPanel User Interface              #"
echo "#                      Please Wait...                      #"
echo "############################################################"
echo ""

# Register Duda Mobile cPanel plugin. Suppress output.
/usr/local/cpanel/bin/register_cpanelplugin /usr/local/cpanel/bin/dudamobile.cpanelplugin >/dev/null 2>&1

echo ""
echo "##########################################################"
echo "################ Installation complete ###################"
echo "##########################################################"
echo ""
echo "cPanel users will now have access to Duda Mobile plugin from"
echo "the 'Software' icon group in cPanel. You can use the feature"
echo "lists in cPanel WHM to see a list of available features."
echo ""
exit 0
