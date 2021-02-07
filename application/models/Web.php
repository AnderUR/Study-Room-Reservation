<?php

class web extends CI_Model
{

    public function get_content($source, $data = false, $headerSrc = 'header', $footerSrc = 'footer', $msg = FALSE)
    {
        if ($source !== FALSE) {
            if ($headerSrc !== "") {
                $this->load->view($headerSrc);
            } else if ($headerSrc == 'headerBasic') {
                $this->load->view($headerSrc);
            }

            $this->load->view($source, $data);

            if ($footerSrc !== "") {
                $this->load->view($footerSrc);
            } else if ($headerSrc == 'footerBasic') {
                $this->load->view($footerSrc);
            }
        } else {
            echo "Source Error";
        }
    }
}
