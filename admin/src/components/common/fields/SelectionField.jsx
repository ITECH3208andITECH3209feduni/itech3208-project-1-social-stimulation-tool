import PropTypes from "prop-types";
import { Field, NativeSelect } from "@chakra-ui/react";

function SelectionField({
    name = "",
    inputPlaceholder = "",
    fieldLabel,
    inputColor = "dark.900",
    borderColor = "whiteAlpha.500",
    onChange,
    defaultValue,
    value,
    items,
    errorText,
}) {
    return (
        <Field.Root invalid={!!errorText}>
            {fieldLabel && <Field.Label>{fieldLabel}</Field.Label>}
            <NativeSelect.Root w="fit-content" minW="250px">
                <NativeSelect.Field
                    bg={inputColor}
                    color={"whiteAlpha.700"}
                    borderWidth="1px"
                    borderColor={borderColor}
                    placeholder={inputPlaceholder}
                    defaultValue={defaultValue}
                    value={value}
                    name={name}
                    onChange={(e) => onChange?.(e)}
                    pl={4}
                >
                    {items.map((item) => {
                        return (
                            <option
                                key={item.id}
                                value={item.id}
                                style={{ background: "#21242D", color: "white" }}
                            >
                                {item.name}
                            </option>
                        );
                    })}
                </NativeSelect.Field>
                <NativeSelect.Indicator />
            </NativeSelect.Root>
            {errorText && <Field.ErrorText>{errorText}</Field.ErrorText>}
        </Field.Root>
    );
}

SelectionField.propTypes = {
    name: PropTypes.string,
    fieldLabel: PropTypes.string,
    inputPlaceholder: PropTypes.string,
    inputColor: PropTypes.string,
    borderColor: PropTypes.string,
    onChange: PropTypes.func,
    defaultValue: PropTypes.string,
    value: PropTypes.string,
    items: PropTypes.array,
};

export default SelectionField;
