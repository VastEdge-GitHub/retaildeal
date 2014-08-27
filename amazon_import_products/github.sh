cd /opt/bitnami/apps/magento/retaildeal/
git pull
rsync "-zvr --exclude '.git' --exclude '.gitignore' /opt/bitnami/apps/magento/retaildeal/ /opt/bitnami/apps/magento/htdocs/" 
