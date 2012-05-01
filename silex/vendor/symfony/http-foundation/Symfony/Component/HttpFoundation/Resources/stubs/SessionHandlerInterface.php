<?php























interface SessionHandlerInterface
{












function open($savePath, $sessionName);








function close();










function read($sessionId);











function write($sessionId, $data);












function destroy($sessionId);












function gc($lifetime);
}
