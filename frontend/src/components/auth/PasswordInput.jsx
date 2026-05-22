import React from "react";
import { useState } from "react";
import { IconButton, Input, InputGroup } from "@chakra-ui/react";
import { IoEyeSharp } from "react-icons/io5";
import { FaEyeSlash } from "react-icons/fa";

function PasswordInput({ name = "", placeholder = "Enter password", onChange }) {
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
                color={"dark.900"}
                borderColor={"gray.400"}
                background="gray.100"
                type={show ? "text" : "password"}
                placeholder={placeholder}
                name={name}
                onChange={(e) => onChange(e)}
            />
        </InputGroup>
    );
}

export default PasswordInput;
