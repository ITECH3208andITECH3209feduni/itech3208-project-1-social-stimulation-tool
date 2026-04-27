import { Field, Input } from "@chakra-ui/react";
import React from "react";
import PropTypes from "prop-types";

function NormalField({ inputPlaceholder = "", fieldLabel, inputColor }) {
    return (
        <Field.Root>
            <Field.Label>{fieldLabel}</Field.Label>
            <Input placeholder={inputPlaceholder} bg={inputColor}/>
        </Field.Root>
    );
}

NormalField.propTypes = {
    inputPlaceholder: PropTypes.string,
    fieldLabel: PropTypes.string,
    inputColor: PropTypes.string
};

export default NormalField;
