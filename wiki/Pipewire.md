# Installing Pipewire

This guide covers the installation and basic service configuration of **Pipewire** on Arix Linux.

---

## 1. Installation

Install the package using pacman

root # ```pacman -S turnstile-dinit pipewire-dinit pipewire-pulse-dinit wireplumber-dinit```

---

## 2. Service Configuration

Once installed, you need to manage the service.

Firtly you need to enable turnstile servise to automaticly start at boot:
root # ```dinitctl enable turnstiled```
root # ```dinitctl start turnstiled```

To start the services immediately:
user $ ```dinitctl --user start pipewire```
user $ ```dinitctl --user start pipewire-pulse```
user $ ```dinitctl --user start wireplumber```

To enable the services to start automatically at boot:
user $ ```dinitctl --user enable pipewire```
user $ ```dinitctl --user enable pipewire-pulse```
user $ ```dinitctl --user enable wireplumber```

---

## 3. Verifying the Installation

To verify that the application is running correctly, you can check its status.

### Check Service Status

root # ```dinitctl --user status pipewire```
root # ```dinitctl --user status pipewire-pulse```
root # ```dinitctl --user status wireplumber```
