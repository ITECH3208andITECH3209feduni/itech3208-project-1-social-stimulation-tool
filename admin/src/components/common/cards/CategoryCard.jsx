import { Card, Box, Flex, HStack, VStack, Heading, Badge, Button, IconButton, Text } from "@chakra-ui/react";
import { FaFolder, FaPlus, FaEdit, FaTrash } from "react-icons/fa";
import SubCategoryItem from "@/components/common/items/SubCategoryItem";

function CategoryCard({
    category,
    onAddSubCategory,
    onEditCategory,
    onDeleteCategory,
    onEditSubCategory,
    onDeleteSubCategory,
}) {
    const subCategories = category.subCategories || [];

    return (
        <Card.Root
            bg="dark.800"
            color="whiteAlpha.900"
            rounded="xl"
            borderWidth="1px"
            borderColor="whiteAlpha.200"
            boxShadow="0 8px 32px rgba(0, 0, 0, 0.2)"
            overflow="hidden"
        >
            {/* Card Header */}
            <Box p={5} bg="whiteAlpha.50" borderBottomWidth="1px" borderColor="whiteAlpha.100">
                <Flex justify="space-between" align="center">
                    <HStack gap={3}>
                        <Box p={2.5} rounded="md" bg="brand.500" color="white">
                            <FaFolder size={18} />
                        </Box>
                        <VStack align="start" gap={0}>
                            <Heading fontSize="lg" fontWeight="bold" color="white">
                                {category.name}
                            </Heading>
                            <Badge px={2} py={0.5} bg="dark.900" color="brand.300" fontSize="xs">
                                {subCategories.length} Sub-categories
                            </Badge>
                        </VStack>
                    </HStack>

                    <HStack gap={1}>
                        <Button
                            size="xs"
                            fontSize="xs"
                            variant="ghost"
                            color="skyblue.300"
                            _hover={{ bg: "whiteAlpha.100", color: "skyblue.200" }}
                            onClick={() => onAddSubCategory(category)}
                        >
                            <FaPlus /> Sub-cat
                        </Button>
                        <IconButton
                            size="xs"
                            variant="ghost"
                            color="gray.400"
                            _hover={{ color: "skyblue.300", bg: "whiteAlpha.100" }}
                            onClick={() => onEditCategory(category)}
                            title="Edit Category"
                            aria-label="Edit Category"
                        >
                            <FaEdit size={12} />
                        </IconButton>
                        <IconButton
                            size="xs"
                            variant="ghost"
                            color="red.400"
                            _hover={{ color: "red.300", bg: "whiteAlpha.100" }}
                            onClick={() => onDeleteCategory(category)}
                            title="Delete Category"
                            aria-label="Delete Category"
                        >
                            <FaTrash />
                        </IconButton>
                    </HStack>
                </Flex>
            </Box>

            {/* Card Body: Sub-Categories List */}
            <Card.Body p={5}>
                {subCategories.length === 0 ? (
                    <Text color="gray.500" fontSize="sm" fontStyle="italic">
                        No sub-categories linked yet.
                    </Text>
                ) : (
                    <VStack align="stretch" gap={2}>
                        {subCategories.map((subCat) => (
                            <SubCategoryItem
                                key={subCat.id}
                                subCat={subCat}
                                onEdit={onEditSubCategory}
                                onDelete={onDeleteSubCategory}
                            />
                        ))}
                    </VStack>
                )}
            </Card.Body>
        </Card.Root>
    );
}

export default CategoryCard;
