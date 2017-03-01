Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/xenial64"

  config.vm.network "private_network", ip: "192.168.33.44"

  config.vm.provider "virtualbox" do |vb|
     vb.memory = "1024"
  end

  config.vm.provision "install_packages", type: "shell", path: "build/vagrant/install_packages.sh"
  config.vm.provision "install_composer", type: "shell", path: "build/vagrant/install_composer.sh"

  config.vm.provision "shell", inline: <<-SHELL
    set -x
    mysql -pPASSWORD_HERE -u root -e 'CREATE DATABASE vagrant_replicator;'
  SHELL

  config.vm.provision "shell", privileged: false, inline: <<-SHELL
    set -x
    cd /vagrant
    composer install --no-interaction
    ./replicator install
  SHELL

end
