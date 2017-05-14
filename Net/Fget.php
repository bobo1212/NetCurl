<?php

namespace Net;

/**
 */
class Fget {

    public function getPage(string $url) {

        $data = file_get_contents($url);

        if (is_array($http_response_header)) {
            foreach ($http_response_header as $c => $h) {
                if (stristr($h, 'content-encoding') and stristr($h, 'gzip')) {
                    return $this->gzdecode($data);
                }
            }
        }
        return $data;
    }

    private function gzdecode($data) {
        return gzinflate(substr($data, 10, -8));
    }

}
