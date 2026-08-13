import { TagsInput, Span, Field } from "@chakra-ui/react";
import React from "react";
import PropTypes from "prop-types";

function TagsInputField({
    name,
    fieldLabel = "Tags",
    placeholder = "Add tag...",
    value,
    defaultValue = [],
    onChange, // expected to be a function that receives the array of tags
    helpText = "Press Enter or Return to add tag",
    errorText,
}) {
    // Chakra UI TagsInput passes an object `{ value: string[] }` to onValueChange
    const handleValueChange = (e) => {
        if (onChange) {
            onChange(e.value);
        }
    };

    return (
        <Field.Root invalid={!!errorText}>
            <TagsInput.Root
                name={name}
                defaultValue={defaultValue}
                value={value}
                onValueChange={handleValueChange}
                w="fit-content"
                minW="250px"
                maxW="100%"
            >
                {fieldLabel && <TagsInput.Label>{fieldLabel}</TagsInput.Label>}
                <TagsInput.Control
                    bg="dark.900"
                    borderColor="whiteAlpha.500"
                    borderWidth="1px"
                    px={2}
                    py={2}
                    minH="40px"
                    borderRadius="md"
                >
                    <TagsInput.Context>
                        {({ value: contextValue }) =>
                            contextValue.map((tag, index) => (
                                <TagsInput.Item key={index} index={index} value={tag}>
                                    <TagsInput.ItemPreview
                                        bg="dark.800"
                                        color="whiteAlpha.900"
                                        borderWidth="1px"
                                        borderColor="whiteAlpha.300"
                                    >
                                        <TagsInput.ItemText>{tag}</TagsInput.ItemText>
                                        <TagsInput.ItemDeleteTrigger />
                                    </TagsInput.ItemPreview>
                                    <TagsInput.ItemInput />
                                </TagsInput.Item>
                            ))
                        }
                    </TagsInput.Context>
                    <TagsInput.Input 
                        placeholder={placeholder} 
                        pl={2} 
                        color="whiteAlpha.700" 
                        _placeholder={{ color: "whiteAlpha.500" }}
                    />
                </TagsInput.Control>
            </TagsInput.Root>
            {helpText && !errorText && (
                <Span textStyle="xs" color="fg.muted" ms="auto" textAlign={"left"} w="100%">
                    {helpText}
                </Span>
            )}
            {errorText && <Field.ErrorText>{errorText}</Field.ErrorText>}
        </Field.Root>
    );
}

TagsInputField.propTypes = {
    name: PropTypes.string,
    fieldLabel: PropTypes.string,
    placeholder: PropTypes.string,
    defaultValue: PropTypes.array,
    value: PropTypes.array,
    onChange: PropTypes.func,
    helpText: PropTypes.string,
    addOnPaste: PropTypes.bool,
    addOnBlur: PropTypes.bool,
};

export default TagsInputField;
