/etc/udev/rules.d/99-toad.rules
udevadm control --reload-rules && udevadm trigger

# cdc_acm fix for EL302P
echo 103e 049c  2 076d 0006 > /sys/bus/usb/drivers/cdc_acm/new_id
