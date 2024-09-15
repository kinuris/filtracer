import math

n, max_invs, max_diff = list(map(int, input().split()))

if n == 1:
    print(1)
else:
    nums = list(map(int, input().split()))
    nums.sort()

    agg = []

    ci = 0
    cs = 0

    for i in range(1, len(nums)):
        leq = abs(nums[i] - nums[i - 1]) <= max_diff
        if leq:
            ci = i + 1 
        else:
            ci = i 

        if not leq or i == len(nums) - 1:
            agg.append(nums[cs:ci])
            cs = ci

        if not leq and i == len(nums) - 1:
            agg.append([nums[-1]])
            break

    diff_list = []
    for i, _ in enumerate(agg[:-1]):
        diff = agg[i + 1][0] - agg[i][-1]
        diff_list.append(diff)

    diff_list.sort()
    terms = 0

    for diff in diff_list:
        if max_diff == 1:
            consumed = diff - 1
        else:
            consumed = math.ceil(diff / max_diff) - 1

        print(f"{consumed=}")
        print(f"{max_invs=}")

        if max_invs < consumed or max_invs < 1:
            break

        max_invs -= consumed
        terms += 1

    print(f"{int(diff_list[0] / max_diff)=}")
    print(f"{math.ceil(diff_list[0] / max_diff)=}")
    print(len(agg) - terms)