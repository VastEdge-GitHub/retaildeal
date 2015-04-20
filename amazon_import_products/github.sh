cd /home/retail/retaildeal/
git pull
rsync -zvr --exclude '.git' --exclude '.gitignore' --exclude 'magmi' /home/retail/retaildeal/ /home/retail/public_html/