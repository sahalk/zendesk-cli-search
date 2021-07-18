# Zendesk Coding Challenge

## Objective 

A simple command line application to search from the provided data (tickets.json and users.json) and return the results in a human readable format.

Examples have been provided in the PDF. 

## Assumptions

1. A ticket can belong only to 1 user.
2. A user can have multiple tickets.
3. Multiple results will be provided if the search parameters matches more than one record.
4. It is assumed that not all records will have the same number of properties. 

## Getting Started

### Installing PHP 

PHP Installation varies from OS to OS. Please refer to https://www.php.net/manual/en/install.php to get started with the installation for PHP.

Please visit https://www.php.net/manual/en/install.macosx.php to install PHP in Mac.
Please visit https://www.php.net/manual/en/install.windows.php to install PHP in Windows.

### Dependency Management

Install composer in your machine. This is mainly used to execute the test cases. The module used for writing the test cases is "PHPUnit".

Please visit https://getcomposer.org/download/ to download and install composer.


### Extract Code

You may extract the latest code from the git repository.
The following commands can be used to extract the code from git.

1. Create a directory of your choice in your local machine.
2. Open Terminal and navigate to the directory you just created.
2. Enter the following command: "git clone https://github.com/sahalk/zendesk-cli-search.git".

The code will now be present in this directory.


### Installing Dependencies 

Open the directory from your terminal and enter the following code: "composer install". This command will install all the required packages in this directory. 

### Execute - First method (using PHP)

This type of execution mainly requires PHP to be installed in your machine. Once PHP has been installed, you may follow the following steps: 

1. Go to the directory where the code is present from your Terminal.
2. Open the src folder from the terminal. 
3. Run the following command: "php Search.php".
4. This will execute the contents in the file and also produce the output based on the user's inputs. 

### Execute Test Cases - Second method (With Debugger)

Once the packages have been installed, you will be able to run "PHPUnit" to run all the testcases. This project has been developed using a TDD approach. You may use the following command to execute all the test cases: "./vendor/bin/phpunit -c phpunit.xml". 

Additionally, debugging of the code during development was done by using XDebug.

XDebug is a tool that is used to debug PHP code and can be used to develop and debug your code. The can be extremely useful for developers writing code in PHP. It provides an efficient way to debug your code during the development process.

## Have fun! :)
