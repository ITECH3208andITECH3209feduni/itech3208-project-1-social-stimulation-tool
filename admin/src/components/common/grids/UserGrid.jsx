import { Grid, GridItem, For } from "@chakra-ui/react";
import UserCard from "../cards/UserCard";

function UserGrid({ users }) {
    const displayUsers = users && users.length > 0 ? users : Array(6).fill(undefined);

    return (
        <Grid gap={4} templateColumns={{ base: "1fr", md: "repeat(2, 1fr)", lg: "repeat(3, 1fr)" }}>
            <For each={displayUsers}>
                {(user, index) => {
                    return (
                        <GridItem key={user?.id || index}>
                            <UserCard user={user} />
                        </GridItem>
                    );
                }}
            </For>
        </Grid>
    );
}

export default UserGrid;
