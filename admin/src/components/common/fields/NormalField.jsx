import { Field, Input } from "@chakra-ui/react";
import React from "react";
import PropTypes from "prop-types";

function NormalField({
    name = "",
    inputPlaceholder = "",
    fieldLabel,
    inputColor = "dark.900",
    borderColor = "whiteAlpha.500",
    onChange,
    value,
    type = "text",
    errorText,
}) {
    return (
        <Field.Root invalid={!!errorText}>
            <Field.Label>{fieldLabel}</Field.Label>
            <Input
                name={name}
                type={type}
                placeholder={inputPlaceholder}
                color={"whiteAlpha.700"}
                bg={inputColor}
                borderWidth="1px"
                borderColor={borderColor}
                onChange={(e) => onChange?.(e)}
                value={value}
                pl={4}
            />
            {errorText && <Field.ErrorText>{errorText}</Field.ErrorText>}
        </Field.Root>
    );
}

NormalField.propTypes = {
    inputPlaceholder: PropTypes.string,
    fieldLabel: PropTypes.string,
    inputColor: PropTypes.string,
};

export default NormalField;
