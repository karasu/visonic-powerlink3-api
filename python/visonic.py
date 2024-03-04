""" Python module to call RPC visonic api """

import json
import socket

BUFFER_SIZE = 1024

VISONIC_INFO = {
    "ip": "192.168.1.1",
    "port": 8181,
    "user_code": "0000",
    "installer_code": "0000",
    "client_ip": "192.168.1.2",
}

class RPCClient:
    """ Class to make RPC calls """
    
    def __init__(self, host:str='localhost', port:int=8080) -> None:
        self.__sock = None
        self.__address = (host, port)

    def is_connected(self):
        """ Check if we are connected to server """
        try:
            self.__sock.sendall(b'test')
            self.__sock.recv(BUFFER_SIZE)
            return True
        except OSError:
            return False

    def connect(self):
        """ Connect to RPC server """
        try:
            self.__sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            self.__sock.connect(self.__address)
        except EOFError as exc:
            print(exc)
            raise EOFError('Client was not able to connect.') from exc

    def disconnect(self):
        """ Close connection """
        try:
            self.__sock.close()
        except OSError:
            pass

    def __getattr__(self, __name: str):
        def excecute(*args, **kwargs):
            self.__sock.sendall(json.dumps((__name, args, kwargs)).encode())
            response = json.loads(self.__sock.recv(SIZE).decode())
            return response
        return excecute

    def __del__(self):
        try:
            self.__sock.close()
        except OSError:
            pass

class VisonicClient(RPCClient):
    """ Class to call Visonic RPC commands """
    def __init__(self, host):
        super().__init__(host=VISONIC_INFO['ip'], port=VISONIC_INFO['port'])
    
    def register_client(self):
        pass

    def get_log_history(self):
        pass

    getDeviceStatuses
    getDeviceConfig
    getUserCodes
    getUserCode
    getInstallerCode
    getDateTime
    getDate
    getLogHistory
    isPanelConnected
    getApiVersion
    getBatteryLevel
    getConnexionType
    getGsmLevel
    getTagsInfo
    getPanelStatuses
    getPanelState
    getPanelConfig
    getLocationsList
    getLocation
    getLocationInfo
    getMaxDevices
    getMaxDevice
    


