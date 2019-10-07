# Utilities

## Make package

This script will create a release package for the PHP client.

### Define the files to include in the package

Since the release package have to contains only some of the source code files, 
we need to specify the file to include into the `package_contents.txt` file
(one entry per line).

### Create the package

From the php client root, run:

    
    ./make_package.sh 1.0.0.0
   
    
The package will be created in the `ROOT/release` folder 
