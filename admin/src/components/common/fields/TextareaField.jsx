import { Field, Textarea } from "@chakra-ui/react";
import React from "react";
import PropTypes from "prop-types";

function TextareaField({
    name,
    fieldLabel,
    placeholder = "",
    value,
    onChange,
    required = false,
    maxLength,
    rows = 3,
    autoresize = false,
    helpText,
    inputColor = "dark.900",
    borderColor = "whiteAlpha.500",
    errorText,
}) {
    return (
        <Field.Root required={required} invalid={!!errorText}>
            {fieldLabel && <Field.Label>{fieldLabel}</Field.Label>}
            <Textarea
                name={name}
                py={2}
                px={4}
                rows={rows}
                maxLength={maxLength}
                autoresize={autoresize ? "" : undefined} // Chakra UI Textarea uses autoresize as a boolean prop
                bg={inputColor}
                color={"whiteAlpha.700"}
                borderColor={borderColor}
                borderWidth="1px"
                placeholder={placeholder}
                value={value}
                onChange={(e) => onChange?.(e)}
            />
            {helpText && !errorText && <Field.HelperText>{helpText}</Field.HelperText>}
            {errorText && <Field.ErrorText>{errorText}</Field.ErrorText>}
        </Field.Root>
    );
}

TextareaField.propTypes = {
    name: PropTypes.string,
    fieldLabel: PropTypes.string,
    placeholder: PropTypes.string,
    value: PropTypes.string,
    onChange: PropTypes.func,
    required: PropTypes.bool,
    maxLength: PropTypes.number,
    rows: PropTypes.number,
    autoresize: PropTypes.bool,
    helpText: PropTypes.string,
    inputColor: PropTypes.string,
    borderColor: PropTypes.string,
};

export default TextareaField;
