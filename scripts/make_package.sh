#!/usr/bin/env bash

BASE_DIR=$(cd "$(dirname "$0")/../" && pwd)

function help {
    cat <<EOF
ScientiaMobile WURFL Microservice packager
Usage: make_package.sh <version>

    <version>     package version (4 digits)

Example: make_package.sh 1.0.0.0
EOF
}

### Validate arguments ###

if [ $# -ne 1 ]; then
    help
    exit 1;
fi

version=$1

cd $BASE_DIR

release_dir=$BASE_DIR/release

if [ -d $release_dir ]; then
  rm -r $release_dir
fi
mkdir -p $release_dir

zip -r $release_dir/wm-php-$version.zip -@ < $BASE_DIR/scripts/package_contents.txt
