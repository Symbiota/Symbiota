#!/bin/bash
# Written by Greg Post

# Global variables
# Do not change these values unless you know what you are doing
TEMPLATE_SUFFIX='_template'
TEMPLATE_PATHS=('../config/' '../' '../includes/' '../content/collections/reports/')
# Add an 'r' to the begining of the relative path (../&&&) to recursivly make all subdirectories writabe
WRITABLE_PATHS=('../temp' 'r../content/collections' 'r../content/collicon' 'r../content/dwca' \
'r../content/geolocate' 'r../content/imglib' 'r../content/logs' 'r../api/storage/framework' 'r../api/storage/logs')

FORCEWRITE=0
TESTMODE=0
VERBOSE=0
BASHTOOOLD=0

options=$(getopt -o fhtv -l force,test,help,verbose -n "$SCRIPTNAME" -- "$@")

#sanity checks

if ((BASH_VERSINFO[0] < 3))
then
  BASHTOOOLD=1
elif ((BASH_VERSINFO[0] < 5))
then
  if ((BASH_VERSINFO[1] < 4))
  then
    BASHTOOOLD=1
  fi
fi

if [[ "$BASHTOOOLD" == "1" ]]
then
  echo "Sorry, you need at least bash-4.4 to run this script.  Please use setup_pre_4.4.bash"
  exit 1
fi

currentDir=${PWD##*/}
if [ "$currentDir" != "config" ]
then
  echo "This script should be executed in the 'Symbiota'/config folder"
  exit 1
fi

#functions
usage(){ # Function: Print a help message.
  echo "  Usage: $SCRIPTNAME [-h|--help -t|--test -f|--force -v|--verbose]" 1>&2
}

printHelp(){
    echo
    echo 'Symbiota setup script'
    echo
    usage
cat <<End-of-message

This script creates initial files that can then be customized/configured as desired

    Optional paramters:

        -h |--help     Print this help screen
        -t |--test     Test execution - makes no changes
        -f |--force    Force overwrite - will not prompt if a file will be overwritten **DANGER**
        -v |--verbose  More verbose output
End-of-message
    exit 0
}

copyFromTemplate(){

    local destinationPath="${1}"
    local regX='(.*)'"$TEMPLATE_SUFFIX"'(.*)'
    local templateArray
    
    echo
    echo "Searching ${destinationPath} for templates"
    readarray -d '' templateArray < <(find "${destinationPath}" -maxdepth 1 -name '*'"${TEMPLATE_SUFFIX}"'*'  -print0)
    for i in "${templateArray[@]}"
    do
      echo "found file: $i"
      if [[ $i =~ $regX ]]
      then 
        local destinationFile=${BASH_REMATCH[1]}${BASH_REMATCH[2]}
      else
        echo "Error: Could not calculate target filename"
        exit 1
      fi

      if [ -f "${destinationFile}" ]
      then
        echo "File ${destinationFile} already exists"
        if [ "$FORCEWRITE" -eq "0" ]
        then
          continue
        fi
      fi

      if [ "$TESTMODE" -eq "1" ]
      then
        echo "cp ${i} ${destinationFile}"
        continue
      fi

      if cp "${i}" "${destinationFile}"
      then
         echo "Copied ${i} to ${destinationFile}"
      else
        echo "Error copying ${i} to ${destinationFile}"
      fi

    done
}

# Main

eval set -- "${options}"
unset options

for var in "$@"
do
  case "$var" in
    -f | --force )
      FORCEWRITE=1; shift ;;
    -t | --test )
      TESTMODE=1; shift ;;
    -v | --verbose )
      VERBOSE=1; shift ;;
    -h | --help )
      printHelp; shift ;;
  esac
done

if [[ "$FORCEWRITE" == "1" && "$TESTMODE" == "1" ]]
then
  echo
  echo 'Error: Cannot set both "test" and "force" modes at the same time.'
  usage
  exit 1
fi

if [[ "$TESTMODE" == "1" ]]
then
  echo
  echo '*******'
  echo 'Test Mode - no changes will be made'
  echo
  echo '*******'
fi

# Iterate over list of paths that contain template files
echo
echo "** Copying template files to destination"
for relPath in "${TEMPLATE_PATHS[@]}"
do
  if ! copyFromTemplate "${relPath}"
  then
    echo "An error occured when processing ${relPath}"
  fi
done

#Adjust file permission to give write access to certain folders and files
echo
echo "** Adjusting file permissions"

for wPath in "${WRITABLE_PATHS[@]}"
do
  echo
  echo "Setting subdirectories of ${wPath} to be writable"

  readarray -d '' writableDirs < <(find "${wPath}" -type d -print0)

  for wDir in ${writableDirs[@]}
  do
    if [[ "$VERBOSE" == "1" ]]
    then
      echo "chmod 777 $wDir"
    fi

    if [[ "$TESTMODE" == "1" ]]
    then
      continue
    fi

    if [[ ${wDir:0:3} == "r.." ]] ; 
    then
      wDir="${wDir:1}" 
      if ! chmod --recursive 777 "$wDir"
      then
        echo "Error setting permission recursively on $wDir"
      fi
    else
      if ! chmod 777 "$wDir"
      then
        echo "Error setting permission on $wDir"
      fi
    fi
  done
done


exit 0

