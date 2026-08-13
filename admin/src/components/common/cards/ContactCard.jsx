import StatusBadge from "@/components/badges/StatusBadge";
import { Card, Text, VStack, HStack, Icon, Badge, Spacer, Button, Box } from "@chakra-ui/react";
import { FaEnvelope, FaPhone, FaMapMarkerAlt, FaCircle } from "react-icons/fa";
import UserAvatar from "@/components/common/avatar/UserAvatar";

function ContactCard({ contact }) {
    return (
        <Card.Root bg="dark.800" color="whiteAlpha.900" rounded="md" h={"350px"}>
            {/* Header */}
            <Card.Header>
                <HStack gap={3}>
                    <UserAvatar src={contact.user.avatar?.url} name={contact.user.username} size="md" />
                    <Card.Title fontSize="xl">{contact.user.username}</Card.Title>
                    <Spacer />
                    <StatusBadge status={contact.status} />
                </HStack>
            </Card.Header>

            {/* Body */}
            <Card.Body>
                <VStack align="start" gap={3}>
                    {/* Email */}
                    <HStack fontSize="sm" color="gray.300">
                        <Icon as={FaEnvelope} />
                        <Text>{contact.user.email}</Text>
                    </HStack>

                    {/* Phone (optional) */}
                    {contact.user.phone && (
                        <HStack fontSize="sm" color="gray.300">
                            <Icon as={FaPhone} />
                            <Text>{contact.user.phone}</Text>
                        </HStack>
                    )}

                    {/* Address (optional) */}
                    {contact.user.address && (
                        <HStack fontSize="sm" color="gray.300">
                            <Icon as={FaMapMarkerAlt} />
                            <Text>{contact.user.address}</Text>
                        </HStack>
                    )}

                    {/* Description */}
                    <Text fontSize="sm" color="gray.400">
                        {contact.message}
                    </Text>
                </VStack>
            </Card.Body>

            {/* Footer */}
           <Card.Footer>
                <VStack w="100%" gap={2}>
                    <Button
                        bg="#0BC5EA"
                        color="white"
                        w="100%"
                        justifyContent="center"
                    >
                        <HStack gap={2}>
                            <Text>Reply</Text>
                            <FaEnvelope />
                        </HStack>
                    </Button>
                    <Button
                        bg="#0BC5EA"
                        color="white"
                        w="100%"
                        justifyContent="center"
                    >
                        Update Status
                    </Button>
                 </VStack>
            </Card.Footer>
        </Card.Root>
    );
}

export default ContactCard;
