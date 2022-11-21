#!/bin/bash
# TODO: make environment-specific - LOCAL, DEV and PROD

if [ "$#" -lt 2 ]; then
    echo "Please include a project name and email address"
    read -r -p "Project Name: " projName
    read -r -p "Email Address: " emailAddress
else
    projName=$1
    emailAddress=$2
fi

read -r -p "Project Name is $projName? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
        ;;
    *)
        exit
        ;;
esac

read -r -p "Email Address is $emailAddress? [y/N] " responseEmail
case "$responseEmail" in
    [yY][eE][sS]|[yY]) 
        ;;
    *)
        exit
        ;;
esac

dbName=$projName

echo -e "\e[93mcreating site called $dbName"

echo $emailAddress

dbName=${dbName//$" "/_}

newStr=${dbName,,}

if mysql -uroot -pmakemoney1 -e "create database ${newStr}_dev"; then
    echo -e "\e[32mDatabase ${newStr}_dev created."
else
    echo "Database Creation failed"
    exit
fi

if mysql -u root -pmakemoney1 -e "grant all privileges on ${newStr}_dev.* to '${newStr}'@'localhost' identified by 'makemoney1'" ; then
    echo -e "\e[32mDatabase user ${newStr} created and granted access to ${newStr}_dev."
    echo -e "\e[93mPassword is makemoney1"
else
    echo "MySQL user creation failed"
    exit
fi

echo -e "\e[93mcreating .env file from template"
cp .env.example .env
echo -e "\e[93mupdating .env file to reference new db"
sed -i "s/starter_shop/$newStr/g" .env
sed -i "s/you@fountainheadme.com/$emailAddress/g" .env

echo -e "\e[93mrunning composer install"
composer install
echo -e "\e[93mrunning npm install"
npm install

echo -e "\e[93mrunning \e[107mphp artisan migrate --seed"
php artisan migrate --seed
echo -e "\e[93mrunning \e[107mphp artisan voyager:install"
php artisan voyager:install
echo -e "\e[93mrunning \e[107mphp artisan voyager:admin --create $emailAddress"
php artisan voyager:admin --create $emailAddress
echo -e "\e[93mrunning \e[107mall set"