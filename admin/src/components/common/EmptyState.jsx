import { Box, Text, VStack, Icon } from "@chakra-ui/react";
import PropTypes from "prop-types";
import { BiBox } from "react-icons/bi";

function EmptyState({
    title = "No items found",
    description = "There are no items to display at the moment.",
    icon = BiBox,
    size = "md",
}) {
    const sizeConfig = {
        sm: { iconSize: 8, titleSize: "lg", descSize: "sm" },
        md: { iconSize: 12, titleSize: "xl", descSize: "md" },
        lg: { iconSize: 16, titleSize: "2xl", descSize: "lg" },
    };

    const config = sizeConfig[size] || sizeConfig.md;

    return (
        <Box display="flex" alignItems="center" justifyContent="center" minH="400px" w="full">
            <VStack spacing={4} textAlign="center">
                <Icon as={icon} boxSize={config.iconSize} color="gray.400" />
                <Text fontSize={config.titleSize} fontWeight="semibold" color="gray.300">
                    {title}
                </Text>
                <Text fontSize={config.descSize} color="gray.500" maxW="md">
                    {description}
                </Text>
            </VStack>
        </Box>
    );
}

EmptyState.propTypes = {
    title: PropTypes.string,
    description: PropTypes.string,
    icon: PropTypes.elementType,
    size: PropTypes.oneOf(["sm", "md", "lg"]),
};

export default EmptyState;
