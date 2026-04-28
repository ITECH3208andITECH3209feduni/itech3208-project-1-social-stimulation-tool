import { Field, Input } from "@chakra-ui/react";
import React from "react";
import PropTypes from "prop-types";

function NormalField({
    inputPlaceholder = "",
    fieldLabel,
    inputColor = "gray.100",
    borderColor = "gray.400",
}) {
    return (
        <Field.Root>
            <Field.Label>{fieldLabel}</Field.Label>
            <Input placeholder={inputPlaceholder} bg={inputColor} borderColor={borderColor} />
        </Field.Root>
    );
}

NormalField.propTypes = {
    inputPlaceholder: PropTypes.string,
    fieldLabel: PropTypes.string,
    inputColor: PropTypes.string,
};

export default NormalField;
