<?php
$isStandalone = !defined('ROUTER_ACTIVE');
if ($isStandalone) {
    require_once __DIR__ . '/config.php';
    $page = 'installation';
    include __DIR__ . '/includes/header.php';
}
?>

<h1>Installation Guide</h1>

<div class="note">
    <p><strong>Note:</strong> Use the <code>arix-install</code> script for a guided, automated installation experience.</p>
</div>

<h2>Update Keyring</h2>
<p>Before proceeding with the installation, it is necessary to update the repository keys:</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">pacman -Sy artixlinux-keyring</span>
    </div>
</div>
<p class="comment">This prevents package signature errors during the installation process. This step is required for both manual and script-based installation.</p>

<h2>Manual Installation</h2>
<p>This guide covers manual installation through the terminal. The installation images work on both BIOS and UEFI systems.</p>

<h3>Set the Keyboard Layout</h3>
<p>First, check available keyboard layouts:</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">ls -R /usr/share/kbd/keymaps</span>
    </div>
</div>
<p class="comment">This command lists all available keyboard layouts in the system.</p>

<p>Then set your preferred layout (for example, Spanish):</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">loadkeys es</span>
    </div>
</div>
<p class="comment">Replace "es" with your preferred layout code (e.g., "us" for US English).</p>

<h3>Partition Your Disk</h3>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">cfdisk /dev/sda</span>
    </div>
</div>
<p class="comment">Launch the cfdisk partitioning tool. Replace /dev/sda with your target disk.</p>

<div class="note">
    <p><strong>UEFI Note:</strong> On UEFI systems with GPT, create an EFI system partition (ESP) of about 512 MiB.</p>
</div>

<h3>Format Partitions</h3>
<p>Format your root, home, and swap partitions:</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">mkfs.ext4 -L ROOT /dev/sda2</span>
        <span class="command-line">mkfs.ext4 -L HOME /dev/sda3 (if created)</span>
        <span class="command-line">mkswap -L SWAP /dev/sda4 (if created)</span>
    </div>
</div>
<p class="comment">Replace /dev/sda2, /dev/sda3, /dev/sda4 with your actual partition numbers. Home and swap partitions are optional.</p>

<p>For UEFI installation, format ESP as FAT32:</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">mkfs.fat -F 32 /dev/sda1</span>
        <span class="command-line">fatlabel /dev/sda1 ESP</span>
    </div>
</div>
<p class="comment">The EFI system partition must be FAT32 formatted.</p>

<h3>Mount Partitions</h3>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">swapon /dev/disk/by-label/SWAP (if created)</span>
        <span class="command-line">mount /dev/disk/by-label/ROOT /mnt</span>
        <span class="command-line">mount --mkdir /dev/disk/by-label/HOME /mnt/home (if created)</span>
        <span class="command-line">mount --mkdir /dev/disk/by-label/ESP /mnt/boot</span>
    </div>
</div>
<p class="comment">Activate swap, mount root partition, and create /boot directory for ESP.</p>

<h3>Connect to the Internet</h3>
<p>Verify your connection:</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">ping google.com</span>
    </div>
</div>
<p class="comment">Press Ctrl+C to stop pinging. If you get responses, you're connected.</p>

<h3>Update System Clock</h3>
<p>Activate NTP daemon to synchronize system clock:</p>

<div class="selector-group">
    <div class="tabs">
        <div class="tab active" data-target="dinit-clock">dinit</div>
        <div class="tab" data-target="openrc-clock">OpenRC</div>
        <div class="tab" data-target="runit-clock">Runit</div>
        <div class="tab" data-target="s6-clock">s6</div>
    </div>
    <div class="tab-panels">
        <div id="dinit-clock" class="tab-content active">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">dinitctl start ntpd</span>
                </div>
            </div>
            <p class="comment">Start the NTP service using dinit.</p>
        </div>
        <div id="openrc-clock" class="tab-content">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">rc-service ntpd start</span>
                </div>
            </div>
            <p class="comment">Start the NTP service to sync system time.</p>
        </div>
        <div id="runit-clock" class="tab-content">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">sv up openntpd</span>
                </div>
            </div>
            <p class="comment">Start the NTP service using runit.</p>
        </div>
        <div id="s6-clock" class="tab-content">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">s6-rc -u change openntpd</span>
                </div>
            </div>
            <p class="comment">Start the NTP service using s6.</p>
        </div>
    </div>
</div>

<h3>Install Base System</h3>
<p>Select your init system and install base packages:</p>

<div class="selector-group">
    <div class="tabs">
        <div class="tab active" data-target="dinit-install">dinit</div>
        <div class="tab" data-target="openrc-install">OpenRC</div>
        <div class="tab" data-target="runit-install">Runit</div>
        <div class="tab" data-target="s6-install">s6</div>
    </div>
    <div class="tab-panels">
        <div id="dinit-install" class="tab-content active">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">basestrap /mnt base base-devel dinit elogind-dinit</span>
                </div>
            </div>
            <p class="comment">Install base system with dinit init system.</p>
        </div>
        <div id="openrc-install" class="tab-content">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">basestrap /mnt base base-devel openrc elogind-openrc</span>
                </div>
            </div>
            <p class="comment">Install base system with OpenRC init system.</p>
        </div>
        <div id="runit-install" class="tab-content">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">basestrap /mnt base base-devel runit elogind-runit</span>
                </div>
            </div>
            <p class="comment">Install base system with runit init system.</p>
        </div>
        <div id="s6-install" class="tab-content">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">basestrap /mnt base base-devel s6-base elogind-s6</span>
                </div>
            </div>
            <p class="comment">Install base system with s6 init system.</p>
        </div>
    </div>
</div>

<h3>Install Kernel</h3>
<p>Select your kernel:</p>

<div class="selector-group">
    <div class="tabs">
        <div class="tab active" data-target="linux-kernel">Linux</div>
        <div class="tab" data-target="lts-kernel">Linux LTS</div>
        <div class="tab" data-target="zen-kernel">Linux Zen</div>
    </div>
    <div class="tab-panels">
        <div id="linux-kernel" class="tab-content active">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">basestrap /mnt linux linux-firmware</span>
                </div>
            </div>
            <p class="comment">Install standard Linux kernel with firmware.</p>
        </div>
        <div id="lts-kernel" class="tab-content">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">basestrap /mnt linux-lts linux-firmware</span>
                </div>
            </div>
            <p class="comment">Install Long-Term Support (LTS) kernel.</p>
        </div>
        <div id="zen-kernel" class="tab-content">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">basestrap /mnt linux-zen linux-firmware</span>
                </div>
            </div>
            <p class="comment">Install Zen kernel (optimized for desktop performance).</p>
        </div>
    </div>
</div>

<p>Generate fstab and chroot into the new system:</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">fstabgen -U /mnt >> /mnt/etc/fstab</span>
        <span class="command-line">arix-chroot /mnt</span>
    </div>
</div>
<p class="comment">Generate filesystem table and enter the new system environment.</p>

<h2>Configure Base System</h2>

<h3>System Clock Configuration</h3>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">ln -sf /usr/share/zoneinfo/Region/City /etc/localtime</span>
        <span class="command-line">hwclock --systohc</span>
    </div>
</div>
<p class="comment">Set timezone (replace Region/City with your location, e.g., America/New_York).</p>

<h3>Localization</h3>
<p>Edit locale configuration file:</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">nano /etc/locale.gen</span>
    </div>
</div>
<p class="comment">Open the file in nano text editor. Remove # from lines for locales you want to enable.</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">locale-gen</span>
    </div>
</div>
<p class="comment">Generate the locales you enabled in the previous step.</p>

<p>Set the system locale:</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">nano /etc/locale.conf</span>
    </div>
</div>
<p class="comment">Open /etc/locale.conf and add the following lines:</p>
<pre>export LANG="en_US.UTF-8"
export LC_COLLATE="C"</pre>
<p class="comment">Configure the system locale settings. Replace "en_US.UTF-8" with your preferred locale.</p>

<h3>Configure Hostname</h3>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">echo "myhostname" > /etc/hostname</span>
    </div>
</div>
<p class="comment">Set hostname (replace "myhostname" with your desired hostname).</p>

<h3>Configure /etc/hosts</h3>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">nano /etc/hosts</span>
    </div>
</div>
<p class="comment">Open /etc/hosts in nano and make sure it looks similar to this (replace "myhostname" with your actual hostname):</p>
<pre>127.0.0.1        localhost
::1              localhost
127.0.1.1        myhostname.localdomain myhostname</pre>

<h3>Change Password & Add a User</h3>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">passwd</span>
    </div>
</div>
<p class="comment">Set root password (enter twice).</p>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">useradd -m user</span>
        <span class="command-line">passwd user</span>
    </div>
</div>
<p class="comment">Create a new user (replace "user" with your username) and set its password.</p>

<h3>Boot Loader</h3>
<p>Select your boot type:</p>

<div class="selector-group">
    <div class="tabs">
        <div class="tab active" data-target="bios-boot">BIOS</div>
        <div class="tab" data-target="uefi-boot">UEFI</div>
    </div>
    <div class="tab-panels">
        <div id="bios-boot" class="tab-content active">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">pacman -S grub</span>
                    <span class="command-line">grub-install --recheck /dev/sda</span>
                    <span class="command-line">grub-mkconfig -o /boot/grub/grub.cfg</span>
                </div>
            </div>
            <p class="comment">Install GRUB bootloader for BIOS systems.</p>
        </div>
        <div id="uefi-boot" class="tab-content">
            <div class="command-wrapper">
                <button class="copy-btn">[Copy]</button>
                <div class="command-block">
                    <span class="command-line">pacman -S grub efibootmgr</span>
                    <span class="command-line">grub-install --target=x86_64-efi --efi-directory=/boot --bootloader-id=grub</span>
                    <span class="command-line">grub-mkconfig -o /boot/grub/grub.cfg</span>
                </div>
            </div>
            <p class="comment">Install GRUB bootloader for UEFI systems.</p>
        </div>
    </div>
</div>

<h3>Network Configuration</h3>
<p>Select network manager:</p>

<div class="selector-group">
    <div class="tabs network-selector">
        <div class="tab active" data-target="dhcpcd-network">DHCPCD</div>
        <div class="tab" data-target="networkmanager-network">NetworkManager</div>
    </div>
    <div class="tab-panels">
        <div id="dhcpcd-network" class="tab-content active network-content">
            <p>Select for your init system:</p>
            <div class="selector-group">
                <div class="tabs">
                    <div class="tab active" data-target="dhcpcd-dinit">dinit</div>
                    <div class="tab" data-target="dhcpcd-openrc">OpenRC</div>
                    <div class="tab" data-target="dhcpcd-runit">Runit</div>
                    <div class="tab" data-target="dhcpcd-s6">s6</div>
                </div>
                <div class="tab-panels">
                    <div id="dhcpcd-dinit" class="tab-content active">
                        <div class="command-wrapper">
                            <button class="copy-btn">[Copy]</button>
                            <div class="command-block">
                                <span class="command-line">pacman -S dhcpcd dhcpcd-dinit</span>
                                <span class="command-line">ln -s /etc/dinit.d/dhcpcd /etc/dinit.d/boot.d/</span>
                            </div>
                        </div>
                        <p class="comment">Install and enable DHCP client for dinit.</p>
                    </div>
                    <div id="dhcpcd-openrc" class="tab-content">
                        <div class="command-wrapper">
                            <button class="copy-btn">[Copy]</button>
                            <div class="command-block">
                                <span class="command-line">pacman -S dhcpcd dhcpcd-openrc</span>
                                <span class="command-line">rc-update add dhcpcd</span>
                            </div>
                        </div>
                        <p class="comment">Install and enable DHCP client for OpenRC.</p>
                    </div>
                    <div id="dhcpcd-runit" class="tab-content">
                        <div class="command-wrapper">
                            <button class="copy-btn">[Copy]</button>
                            <div class="command-block">
                                <span class="command-line">pacman -S dhcpcd dhcpcd-runit</span>
                                <span class="command-line">ln -s /etc/runit/sv/dhcpcd /etc/runit/runsvdir/default</span>
                            </div>
                        </div>
                        <p class="comment">Install and enable DHCP client for runit.</p>
                    </div>
                    <div id="dhcpcd-s6" class="tab-content">
                        <div class="command-wrapper">
                            <button class="copy-btn">[Copy]</button>
                            <div class="command-block">
                                <span class="command-line">pacman -S dhcpcd dhcpcd-s6</span>
                                <span class="command-line">touch /etc/s6/adminsv/default/contents.d/dhcpcd</span>
                                <span class="command-line">s6-db-reload</span>
                            </div>
                        </div>
                        <p class="comment">Install and enable DHCP client for s6.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="networkmanager-network" class="tab-content network-content">
            <p>Select for your init system:</p>
            <div class="selector-group">
                <div class="tabs">
                    <div class="tab active" data-target="nm-dinit">dinit</div>
                    <div class="tab" data-target="nm-openrc">OpenRC</div>
                    <div class="tab" data-target="nm-runit">Runit</div>
                    <div class="tab" data-target="nm-s6">s6</div>
                </div>
                <div class="tab-panels">
                    <div id="nm-dinit" class="tab-content active">
                        <div class="command-wrapper">
                            <button class="copy-btn">[Copy]</button>
                            <div class="command-block">
                                <span class="command-line">pacman -S networkmanager networkmanager-dinit</span>
                                <span class="command-line">ln -s /etc/dinit.d/NetworkManager /etc/dinit.d/boot.d/</span>
                            </div>
                        </div>
                        <p class="comment">Install and enable NetworkManager for dinit.</p>
                    </div>
                    <div id="nm-openrc" class="tab-content">
                        <div class="command-wrapper">
                            <button class="copy-btn">[Copy]</button>
                            <div class="command-block">
                                <span class="command-line">pacman -S networkmanager networkmanager-openrc</span>
                                <span class="command-line">rc-update add NetworkManager</span>
                            </div>
                        </div>
                        <p class="comment">Install and enable NetworkManager for OpenRC.</p>
                    </div>
                    <div id="nm-runit" class="tab-content">
                        <div class="command-wrapper">
                            <button class="copy-btn">[Copy]</button>
                            <div class="command-block">
                                <span class="command-line">pacman -S networkmanager networkmanager-runit</span>
                                <span class="command-line">ln -s /etc/runit/sv/NetworkManager /etc/runit/runsvdir/default</span>
                            </div>
                        </div>
                        <p class="comment">Install and enable NetworkManager for runit.</p>
                    </div>
                    <div id="nm-s6" class="tab-content">
                        <div class="command-wrapper">
                            <button class="copy-btn">[Copy]</button>
                            <div class="command-block">
                                <span class="command-line">pacman -S networkmanager networkmanager-s6</span>
                                <span class="command-line">touch /etc/s6/adminsv/default/contents.d/NetworkManager</span>
                                <span class="command-line">s6-db-reload</span>
                            </div>
                        </div>
                        <p class="comment">Install and enable NetworkManager for s6.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h3>Configure Arix Identity</h3>
<div class="warning">
    <p><strong>Important:</strong> If you skip this step, your system will identify itself as Artix Linux instead of Arix Linux.</p>
    <p>Execute these commands to properly configure Arix Linux identity:</p>
</div>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">printf "NAME=\"Arix Linux\"\nPRETTY_NAME=\"Arix Linux\"\nID=arix\nID_LIKE=artix\nANSI_COLOR=\"0;34\"\n" > /etc/os-release</span>
        <span class="command-line">printf "DISTRIB_ID=Arix\nDISTRIB_RELEASE=rolling\nDISTRIB_DESCRIPTION=\"Arix Linux\"\n" > /etc/lsb-release</span>
        <span class="command-line">rm /etc/artix-release</span>
        <span class="command-line">touch /etc/arix-release</span>
        <span class="command-line">sed -i 's/Artix/Arix/g' /etc/default/grub</span>
        <span class="command-line">grub-mkconfig -o /boot/grub/grub.cfg</span>
    </div>
</div>

<h3>Reboot System</h3>
<div class="command-wrapper">
    <button class="copy-btn">[Copy]</button>
    <div class="command-block">
        <span class="command-line">exit</span>
        <span class="command-line">umount -R /mnt</span>
        <span class="command-line">reboot</span>
    </div>
</div>
<p class="comment">Exit chroot, unmount all partitions, and reboot into your new Arix Linux installation.</p>

<h2 style="margin-top: 40px;">Alternative: Using arix-install Script</h2>
<div style="background-color: #2a2a2a; border: 1px solid #444; padding: 25px; margin-top: 20px;">

    <p>For an interactive guided installation, use the <code>arix-install</code> script:</p>
    <div class="command-wrapper">
        <button class="copy-btn">[Copy]</button>
        <div class="command-block">
            <span class="command-line">arix-install</span>
        </div>
    </div>
    <p class="comment">Launch the interactive installation script.</p>

    <p>The script provides a step-by-step interface that guides you through:</p>
    <ul class="feature-list">
        <li>Hostname configuration</li>
        <li>User creation with passwords</li>
        <li>BIOS or UEFI boot selection</li>
        <li>Swap partition configuration</li>
        <li>Time zone selection</li>
        <li>Locale configuration</li>
        <li>Network manager choice (dhcpcd or NetworkManager)</li>
        <li>Audio system selection (PipeWire)</li>
        <li>Desktop environment selection</li>
        <li>Package selection based on choices</li>
    </ul>

    <p style="margin-bottom: 0;">This is the recommended method for new users or those who prefer a guided installation process.</p>
</div>

<?php
if ($isStandalone) include __DIR__ . '/includes/footer.php';
