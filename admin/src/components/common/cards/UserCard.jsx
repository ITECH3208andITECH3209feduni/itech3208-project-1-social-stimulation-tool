import { Card, Text, VStack, HStack, Icon, Spacer, Button, Box, Flex } from "@chakra-ui/react";
import {
    FaEnvelope,
    FaPhone,
    FaMapMarkerAlt,
    FaCheckCircle,
    FaUserTag,
    FaTrash,
    FaEdit,
    FaIdBadge,
} from "react-icons/fa";
import StatusBadge from "@/components/badges/StatusBadge";
import UserAvatar from "@/components/common/avatar/UserAvatar";

function UserCard({ user = sampleUser }) {
    return (
        <Card.Root
            bg="dark.800"
            color="whiteAlpha.900"
            rounded="xl"
            overflow="hidden"
            borderWidth="1px"
            borderColor="whiteAlpha.200"
            boxShadow="0 8px 32px rgba(0, 0, 0, 0.3)"
            position="relative"
            w="full"
        >
            {/* Glassmorphism subtle glow effect */}
            <Box
                position="absolute"
                top="-50px"
                right="-50px"
                w="150px"
                h="150px"
                bg="brand.500"
                filter="blur(80px)"
                opacity={0.3}
                borderRadius="full"
                zIndex={0}
            />

            <Box p={6} position="relative" zIndex={1}>
                {/* ID & Status Row */}
                <Flex justify="space-between" align="center" mb={6}>
                    <HStack color="whiteAlpha.500" fontSize="sm">
                        <Icon as={FaIdBadge} />
                        <Text fontFamily="monospace">
                            ID: {user.id ? user.id.slice(-6).toUpperCase() : "N/A"}
                        </Text>
                    </HStack>
                    <StatusBadge status={user.status} />
                </Flex>

                {/* Avatar & Name Section */}
                <Flex gap={5} align="center" mb={6}>
                    <Box
                        p="3px"
                        borderRadius="full"
                        bgGradient="to-br"
                        gradientFrom="brand.500"
                        gradientTo="blue.500"
                    >
                        <UserAvatar
                            src={user.avatar?.url}
                            name={user.username}
                            size="xl"
                            border="3px solid"
                            borderColor="dark.800"
                        />
                    </Box>
                    <VStack align="start" gap={0}>
                        <Text fontSize="2xl" fontWeight="bold" color="white">
                            {user.fullName}
                        </Text>
                        <Text fontSize="md" color="brand.300">
                            @{user.username || user.firstName?.toLowerCase()}
                        </Text>
                    </VStack>
                </Flex>

                {/* Info Section */}
                <VStack align="start" gap={3} mb={6}>
                    <HStack fontSize="sm" color="gray.300">
                        <Icon as={FaUserTag} color="whiteAlpha.500" />
                        <Text fontWeight="semibold" color="whiteAlpha.800">
                            Role:
                        </Text>
                        <Text>{user.role || "User"}</Text>
                    </HStack>

                    <HStack fontSize="sm" color="gray.300">
                        <Icon as={FaEnvelope} color="whiteAlpha.500" />
                        <Text>{user.email}</Text>
                    </HStack>

                    {user.phone && (
                        <HStack fontSize="sm" color="gray.300">
                            <Icon as={FaPhone} color="whiteAlpha.500" />
                            <Text>{user.phone}</Text>
                        </HStack>
                    )}

                    {user.location && (
                        <HStack fontSize="sm" color="gray.300">
                            <Icon as={FaMapMarkerAlt} color="whiteAlpha.500" />
                            <Text>{user.location}</Text>
                        </HStack>
                    )}

                    <HStack fontSize="sm" color={user.acceptedTerms ? "green.400" : "gray.400"}>
                        <Icon as={FaCheckCircle} />
                        <Text>{user.acceptedTerms ? "Terms Accepted" : "Terms Not Accepted"}</Text>
                    </HStack>
                </VStack>
            </Box>

            {/* Actions Footer */}
            <Flex
                bg="whiteAlpha.50"
                borderTopWidth="1px"
                borderColor="whiteAlpha.100"
                p={4}
                gap={3}
            >
                <Button flex={1} bg="dark.900" color="red.400">
                    <Icon as={FaTrash} mr={2} /> Delete
                </Button>
                <Button flex={1} bg="brand.500" color="white">
                    <Icon as={FaEdit} mr={2} /> Update
                </Button>
            </Flex>
        </Card.Root>
    );
}

export default UserCard;
