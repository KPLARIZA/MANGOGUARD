-- Run this in Supabase Dashboard -> SQL Editor
-- Creates tables used by the Laravel controllers (users, pest_alerts, crop_harvest)

-- USERS
create table if not exists public.users (
  id uuid primary key,
  name varchar(255) not null,
  email varchar(255) unique not null,
  role varchar(50) default 'farmer',
  created_at timestamp default now(),
  updated_at timestamp default now()
);

alter table public.users enable row level security;

-- Allow authenticated users to read/update their own row (id matches auth.uid())
drop policy if exists "Users can read own data" on public.users;
create policy "Users can read own data" on public.users
  for select using (auth.uid() = id);

drop policy if exists "Users can update own data" on public.users;
create policy "Users can update own data" on public.users
  for update using (auth.uid() = id);


-- PEST ALERTS
create table if not exists public.pest_alerts (
  id uuid primary key default gen_random_uuid(),
  pest_type varchar(100) not null,
  severity varchar(50) not null,
  location jsonb,
  notes text,
  created_at timestamp default now(),
  user_id uuid references public.users(id)
);

alter table public.pest_alerts enable row level security;

drop policy if exists "Anyone can read pest alerts" on public.pest_alerts;
create policy "Anyone can read pest alerts" on public.pest_alerts
  for select using (true);

drop policy if exists "Users can insert pest alerts" on public.pest_alerts;
create policy "Users can insert pest alerts" on public.pest_alerts
  for insert with check (auth.role() = 'authenticated');


-- CROP HARVEST
create table if not exists public.crop_harvest (
  id uuid primary key default gen_random_uuid(),
  volume numeric(10, 2),
  crop_type varchar(100),
  harvest_date date,
  location jsonb,
  created_at timestamp default now(),
  user_id uuid references public.users(id)
);

alter table public.crop_harvest enable row level security;

drop policy if exists "Anyone can read harvest data" on public.crop_harvest;
create policy "Anyone can read harvest data" on public.crop_harvest
  for select using (true);

drop policy if exists "Users can insert harvest data" on public.crop_harvest;
create policy "Users can insert harvest data" on public.crop_harvest
  for insert with check (auth.role() = 'authenticated');

