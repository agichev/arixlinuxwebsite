# Installing <package_name>

This guide covers the installation and basic service configuration of **<package_name>** on Gentoo Linux.

---

## 1. Preparation

Before installation, it is recommended to check the USE flags for the package to customize its features.

root # emerge --pretend --verbose <package_name>

*(Optional)* If you need to modify USE flags, add them to your `package.use` file:

root # echo "category/<package_name> flag1 flag2" >> /etc/portage/package.use/<package_name>

---

## 2. Installation

Install the package using Portage, Gentoo's package manager:

root # emerge --ask category/<package_name>

---

## 3. Service Configuration

Once installed, you need to manage the service. Choose the section below that matches your init system (OpenRC or systemd).

### OpenRC (Default)

To start the service immediately:
root # rc-service <service_name> start

To enable the service to start automatically at boot:
root # rc-update add <service_name> default

### systemd

To start the service immediately:
root # systemctl start <service_name>

To enable the service to start automatically at boot:
root # systemctl enable <service_name>

---

## 4. Verifying the Installation

To verify that the application is running correctly, you can check its status or run it as a regular user.

### Check Service Status

**OpenRC:**
root # rc-service <service_name> status

**systemd:**
root # systemctl status <service_name>

### Run Application (as regular user)

To check the installed version or run user-level commands:

user $ <package_name> --version
