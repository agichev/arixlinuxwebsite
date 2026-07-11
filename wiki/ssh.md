# Installing Pipewire

This guide covers the installation and basic service configuration of **Pipewire** on Arix Linux. Be careful which user you run commands from.

---

## 1. Installation

Install the package using pacman.

```bash
pacman -S openssh
```

## 2. Usage

**Commands**

OpenSSH provides several commands, see each command's man page for usage information:
- [scp(1)](https://man.archlinux.org/man/scp.1.en) - secure file copy
- sftp(1) - secure file transfer
- ssh-add(1) - add private key identities to the authentication agent
- ssh-agent(1) - authentication agent
- ssh-copy-id(1) - use locally available keys to authorize logins on a remote machine
- ssh-keygen(1) - authentication key utility
- ssh-keyscan(1) - gather SSH public keys from servers
- sshd(8) - OpenSSH daemo

---

## 3. Verifying the Installation

To verify that the application is running correctly, you can check its status.

### Check Service Status

```user
dinitctl --user status pipewire
```

```user
dinitctl --user status pipewire-pulse
```

```user
dinitctl --user status wireplumber
