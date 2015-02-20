cd /home/retail/retaildeal/
git pull
rsync -zvr --exclude '.git' --exclude '.gitignore' /home/retail/retaildeal/ /home/retail/public_html/
