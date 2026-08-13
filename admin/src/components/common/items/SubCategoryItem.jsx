import { Flex, VStack, HStack, Text, IconButton } from "@chakra-ui/react";
import { FaTag, FaEdit, FaTrash } from "react-icons/fa";

function SubCategoryItem({ subCat, onEdit, onDelete }) {
    return (
        <Flex
            p={3}
            rounded="lg"
            bg="dark.900"
            borderWidth="1px"
            borderColor="whiteAlpha.100"
            justify="space-between"
            align="center"
            _hover={{ borderColor: "brand.500" }}
            transition="all 0.2s"
        >
            <VStack align="start" gap={0} flex={1} mr={2}>
                <HStack gap={2}>
                    <FaTag size={12} color="#4299E1" />
                    <Text fontSize="sm" fontWeight="semibold" color="white">
                        {subCat.name}
                    </Text>
                </HStack>
                {subCat.description && (
                    <Text fontSize="xs" color="gray.400" noOfLines={1} ml={5}>
                        {subCat.description}
                    </Text>
                )}
            </VStack>

            <HStack gap={1}>
                <IconButton
                    size="xs"
                    variant="ghost"
                    color="gray.400"
                    _hover={{ color: "skyblue.300" }}
                    onClick={() => onEdit(subCat)}
                    aria-label="Edit Sub-category"
                >
                    <FaEdit size={12} />
                </IconButton>
                <IconButton
                    size="xs"
                    variant="ghost"
                    color="gray.400"
                    _hover={{ color: "red.400" }}
                    onClick={() => onDelete(subCat)}
                    aria-label="Delete Sub-category"
                >
                    <FaTrash size={12} />
                </IconButton>
            </HStack>
        </Flex>
    );
}

export default SubCategoryItem;
