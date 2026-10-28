## myITS Broadcast
myITSBroadcast is a student bulletin board web website designed to centralize campus-related information for the Institut Teknologi Sepuluh Nopember (ITS) community. It allows users to publish and discover announcements and events through a public feed, search and filter content, open post details, and interact with published posts through comments and bookmarks. Authenticated users can create and manage their own posts and use a personal dashboard to review their content and saved posts.

The website is built with Laravel and follows its Model--View--Controller (MVC) structure. The current project uses a Livewire-based Laravel starter kit, Blade views, Tailwind CSS, and SQLite for local persistence. The database defines relationships between users, posts, categories, comments, and bookmarks, including multiple foreign-key constraints.

Built by three students as a midterm project of web programming class.
| Name | NRP | Contribution |
|---|---|---|
| Marvel Timothy N. | 5025251209  | Backend, database design, authentication implementation |
| Joel Angga Fransmartua Manalu | 5025251212 | Features, testing, final Report |
| Syabil Zihni | 5025251217 | Frontend, UI/UX, and project documentation. |

## Main Features
- Announcement feed
- Categories and post types
- Search and filtering
- Post detail pages
- Create posts
- Edit and delete posts
- Comments
- Bookmarks
- Personal dashboard

## Conceptual Data Model (CDM)

```mermaid
erDiagram
    USER ||--o{ POST : creates
    CATEGORY ||--o{ POST : categorizes
    USER ||--o{ COMMENT : writes
    POST ||--o{ COMMENT : contains
    COMMENT o|--o{ COMMENT : has_replies
    USER ||--o{ BOOKMARK : creates
    POST ||--o{ BOOKMARK : saved_as

    USER {
        int id
        string name
        string email
    }

    CATEGORY {
        int id
        string name
        string slug
        string description
    }

    POST {
        int id
        string title
        string body
        string type
        string status
        datetime published_at
    }

    COMMENT {
        int id
        string body
        int parent_id
    }

    BOOKMARK {
        datetime created_at
    }
```

## Physical Data Model (PDM)

```mermaid
erDiagram
    USERS ||--o{ POSTS : user_id
    CATEGORIES ||--o{ POSTS : category_id
    USERS ||--o{ COMMENTS : user_id
    POSTS ||--o{ COMMENTS : post_id
    COMMENTS o|--o{ COMMENTS : parent_id
    USERS ||--o{ BOOKMARKS : user_id
    POSTS ||--o{ BOOKMARKS : post_id
    USERS ||--o{ PASSKEYS : user_id
    USERS ||--o{ EVENT_REGISTRATIONS : user_id
    POSTS ||--o{ EVENT_REGISTRATIONS : post_id

    USERS {
        integer id PK
        varchar name
        varchar email UK
        datetime email_verified_at
        varchar password
        varchar remember_token
        datetime created_at
        datetime updated_at
        text two_factor_secret
        text two_factor_recovery_codes
        datetime two_factor_confirmed_at
        tinyint is_moderator
        varchar avatar_path
    }

    CATEGORIES {
        integer id PK
        varchar name UK
        varchar slug UK
        text description
        datetime created_at
        datetime updated_at
    }

    POSTS {
        integer id PK
        integer user_id FK
        integer category_id FK
        varchar type
        varchar status
        varchar title
        varchar slug UK
        text body
        tinyint is_pinned
        datetime published_at
        datetime event_starts_at
        datetime event_ends_at
        varchar event_location
        datetime created_at
        datetime updated_at
        varchar cover_image_path
    }

    COMMENTS {
        integer id PK
        integer post_id FK
        integer user_id FK
        text body
        datetime created_at
        datetime updated_at
        integer parent_id FK
    }

    BOOKMARKS {
        integer id PK
        integer user_id FK
        integer post_id FK
        datetime created_at
        datetime updated_at
    }

    PASSKEYS {
        integer id PK
        integer user_id FK
        varchar name
        varchar credential_id UK
        text credential
        datetime last_used_at
        datetime created_at
        datetime updated_at
    }

    EVENT_REGISTRATIONS {
        integer id PK
        integer post_id FK
        integer user_id FK
        datetime created_at
        datetime updated_at
    }
```
