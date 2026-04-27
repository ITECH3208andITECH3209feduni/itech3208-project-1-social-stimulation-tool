import React from "react";
import { useState } from "react";
import { IconButton, Input, InputGroup } from "@chakra-ui/react";
import { IoEyeSharp } from "react-icons/io5";
import { FaEyeSlash } from "react-icons/fa";

function PasswordInput({ placeholder = "Enter password" }) {
    const [show, setShow] = useState(false);

    return (
        <InputGroup
            endElement={
                <IconButton
                    background={"transparent"}
                    color={"gray.500"}
                    cursor="pointer"
                    onClick={() => {
                        setShow(!show);
                    }}
                >
                    {show ? <FaEyeSlash /> : <IoEyeSharp />}
                </IconButton>
            }
        >
            <Input
                background="gray.100"
                type={show ? "text" : "password"}
                placeholder={placeholder}
            />
        </InputGroup>
    );
}

export default PasswordInput;
