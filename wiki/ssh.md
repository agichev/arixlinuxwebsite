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
- [sftp(1)](https://man.archlinux.org/man/sftp.1.en) - secure file transfer
- [ssh-add(1)](https://man.archlinux.org/man/ssh-add.1.en) - add private key identities to the authentication agent
- [ssh-agent(1)](https://man.archlinux.org/man/ssh-agent.1.en) - authentication agent
- [ssh-copy-id(1)](https://linux.die.net/man/1/ssh-copy-id) - use locally available keys to authorize logins on a remote machine
- [ssh-keygen(1)](https://man.archlinux.org/man/ssh-keygen.1.en) - authentication key utility
- [ssh-keyscan(1)](https://man.archlinux.org/man/ssh-keyscan.1.en) - gather SSH public keys from servers
- [sshd(8)](https://man.archlinux.org/man/sshd.8.en) - OpenSSH daemon

---

## 3. Escape sequences

During an active SSH session, pressing the tilde (~) key starts an escape sequence. Enter the following for a list of options: 

```ssh
~?
```
