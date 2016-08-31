cat /vagrant/shell_provisioner/files/dot/.bash_aliases > /home/vagrant/.bash_aliases
cat /vagrant/shell_provisioner/files/dot/.bash_git > /home/vagrant/.bash_git
cat /vagrant/shell_provisioner/files/dot/.bash_profile > /home/vagrant/.bash_profile
cat /vagrant/shell_provisioner/files/dot/.bashrc > /home/vagrant/.bashrc
cat /vagrant/shell_provisioner/files/dot/.vimrc > /home/vagrant/.vimrc

sudo chown vagrant:vagrant /home/vagrant/.*
sudo chmod 664 /home/vagrant/.bash_profile
sudo chmod 664 /home/vagrant/.bashrc

