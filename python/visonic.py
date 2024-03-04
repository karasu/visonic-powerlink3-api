""" Python module to call RPC visonic api """

import xmlrpc.client
#import json
#import socket

VISONIC_INFO = {
    "ip": "192.168.1.1",
    "port": 8181,
    "user_code": "0000",
    "installer_code": "0000",
    "client_ip": "192.168.1.2",
}

METHODS = {
    "log_history": "getLogHistory",
    "device_statuses": "getDeviceStatuses",
    "device_config": "getDeviceConfig",
    "user_codes": "getUserCodes",
    "user_code": "getUserCode",
    "installer_code": "getInstallerCode",
    "date_time": "getDateTime",
    "date": "getDate",
    "api_version": "getApiVersion",
    "battery_level": "getBatteryLevel",
    "connexion_type": "getConnexionType",
    "gsm_level": "getGsmLevel",
    "tags_info": "getTagsInfo",
    "panel_statuses": "getPanelStatuses",
    "panel_state": "getPanelState",
    "panel_config": "getPanelConfig",
    "locations_list": "getLocationsList",
    "location": "getLocation",
    "location_info": "getLocationInfo",
    "max_devices": "getMaxDevices",
    "max_device": "getMaxDevice",
    "eeprom_version": "getEepromVersion",

    ##########################################

    "power_GDevice_property": "getPowerGDeviceProperty",
    "eeprom_item": "getEepromItem",
    "wlan_config": "getWlanConfig",
    "wlan_scan_results": "getWlanScanResults",
    "wlanap_config": "getWlanapConfig",
    "service_led": "getServiceLed",
}

class VisonicClient():
    """ Class to call Visonic RPC commands """

    def __init__(self):
        url = f"http://{VISONIC_INFO['ip']}:{VISONIC_INFO['port']}/remote/json-rpc"
        print(url)
        self.server = xmlrpc.client.ServerProxy(url)
        print(self.server.system.listMethods())

    def register_client(self):
        pass
    def is_panel_connected(self):
        pass
    def is_WPS_enabled(self):
        pass

    def pmax_download(self):
        pass

    def pmax_login(self):
        pass

    def get(self, method):
        """ Calls RPC getMethod method """
        method = METHODS.get(method, None)
        if method:
            func = getattr(self.server('https://example.com/rpc'), method)
            print(func())
        else:
            print("method %s unknown", method)


vc = VisonicClient()
vc.register_client()
print(vc.get("device_statuses"))
