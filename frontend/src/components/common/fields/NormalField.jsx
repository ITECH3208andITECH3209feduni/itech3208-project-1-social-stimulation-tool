import { Field, Input } from "@chakra-ui/react";
import React from "react";
import PropTypes from "prop-types";

function NormalField({
    name = "",
    inputPlaceholder = "",
    fieldLabel,
    inputColor = "gray.100",
    borderColor = "gray.400",
    onChange,
    value,
    type = "text",
}) {
    return (
        <Field.Root>
            <Field.Label>{fieldLabel}</Field.Label>
            <Input
                name={name}
                type={type}
                placeholder={inputPlaceholder}
                color={"dark.700"}
                bg={inputColor}
                borderColor={borderColor}
                onChange={(e) => onChange(e)}
                value={value}
            />
        </Field.Root>
    );
}

NormalField.propTypes = {
    inputPlaceholder: PropTypes.string,
    fieldLabel: PropTypes.string,
    inputColor: PropTypes.string,
};

export default NormalField;
